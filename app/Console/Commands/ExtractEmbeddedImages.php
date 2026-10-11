<?php

namespace App\Console\Commands;

use App\Support\EmbeddedImageExtractor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * DEV-3. Images pasted into the CMS editor were saved inside the rich text as base64 data URIs, which makes a page's HTML
 * several megabytes. This moves them out into real, resized files and puts the file URL in the text.
 *
 * Default is a dry run: nothing is written. With --apply, each record is backed up first (storage/app/embedded-image-backups,
 * a private path), then its images are written, then only the one text column is updated through the query builder, so
 * updated_at, content_updated_at and model events are not touched.
 */
class ExtractEmbeddedImages extends Command
{
    /** Every table and column that can hold editor HTML (the TinyMCE fields of the admin and the public forms). */
    public const COLUMNS = [
        'articles' => ['content', 'sidebar', 'sources'],
        'glossary' => ['content'],
        'content' => ['content', 'sidebar', 'sources'],
        'listings' => ['description'],
        'organizations' => ['description', 'extended_description'],
        'jobs' => ['description', 'qualifications'],
    ];

    public const BACKUP_DIRECTORY = 'embedded-image-backups';

    protected $signature = 'content:extract-embedded-images
        {--apply : Write the files and update the rows. Without it this is a dry run and nothing is written}
        {--only= : Only the record with this slug}
        {--table= : Only this table: articles, glossary, content, listings, organizations or jobs}';

    protected $description = 'Move base64 images pasted into rich text out into resized files (dry run unless --apply)';

    public function handle(EmbeddedImageExtractor $extractor): int
    {
        $apply = (bool) $this->option('apply');
        $only = $this->option('only') ?: null;
        $tableOption = $this->option('table') ?: null;

        if ($tableOption !== null && ! isset(self::COLUMNS[$tableOption])) {
            $this->error('Unknown table "' . $tableOption . '". Use one of: ' . implode(', ', array_keys(self::COLUMNS)));

            return self::FAILURE;
        }

        // Decoding a large pasted image needs more memory than the default CLI limit.
        if ($this->memoryLimitBytes() < 768 * 1024 * 1024 && $this->memoryLimitBytes() !== -1) {
            ini_set('memory_limit', '768M');
        }

        $this->line($apply ? 'APPLYING changes.' : 'DRY RUN: nothing is written. Add --apply to make the changes.');

        $found = 0;
        $failed = 0;
        $before = 0;
        $after = 0;

        foreach (self::COLUMNS as $table => $columns) {
            if ($tableOption !== null && $tableOption !== $table) {
                continue;
            }

            if (! Schema::hasTable($table)) {
                $this->warn('Skipping ' . $table . ': no such table.');

                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                foreach ($this->recordIds($table, $column, $only) as $id) {
                    try {
                        $result = $this->handleRecord($extractor, $table, $column, $id, $apply);
                    } catch (Throwable $e) {
                        $failed++;
                        $this->error(sprintf('FAILED %s #%d %s: %s', $table, $id, $column, $e->getMessage()));

                        continue;
                    }

                    if ($result === null) {
                        continue;
                    }

                    $found++;
                    $before += $result['before'];
                    $after += $result['after'];
                }
            }
        }

        $this->newLine();
        $this->line(sprintf(
            '%d column(s) with embedded images. HTML %s before, %s %s after: %s saved.',
            $found,
            $this->bytes($before),
            $apply ? '' : 'about',
            $this->bytes($after),
            $this->bytes($before - $after),
        ));

        if ($failed > 0) {
            $this->error($failed . ' record(s) failed and were left unchanged.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * @return array{before: int, after: int}|null null when the record has no image to convert
     */
    private function handleRecord(EmbeddedImageExtractor $extractor, string $table, string $column, int $id, bool $apply): ?array
    {
        $row = DB::table($table)->where('id', $id)->first(array_values(array_filter(
            ['id', Schema::hasColumn($table, 'slug') ? 'slug' : null, $column]
        )));

        $original = (string) ($row->{$column} ?? '');

        // Not written yet: this only converts in memory. The backup comes before any file or row is written.
        $result = $extractor->extract($original);

        if ($result['count'] === 0) {
            return null;
        }

        $before = strlen($original);
        $after = strlen($result['html']);

        if ($apply) {
            $backup = self::BACKUP_DIRECTORY . '/' . $table . '-' . $id . '-' . $column . '-' . now()->format('Ymd-His') . '.html';

            if (! Storage::disk('local')->put($backup, $original)) {
                throw new \RuntimeException('Could not write the backup ' . $backup);
            }

            $extractor->store($result);

            // The query builder, and the one column only: no updated_at, no content_updated_at, no model events.
            DB::table($table)->where('id', $id)->update([$column => $result['html']]);
        }

        $this->line(sprintf(
            '%s #%d %s %s: %d image(s), %s -> %s%s%s',
            $table,
            $id,
            $row->slug ?? '(no slug)',
            $column,
            $result['count'],
            $this->bytes($before),
            $apply ? '' : 'about ',
            $this->bytes($after),
            $result['skipped'] > 0 ? ', ' . $result['skipped'] . ' left as they were' : '',
        ));

        return ['before' => $before, 'after' => $after];
    }

    /**
     * Ids only, so a 4 MB value is never held for more than one record at a time.
     *
     * @return array<int, int>
     */
    private function recordIds(string $table, string $column, ?string $slug): array
    {
        $query = DB::table($table)->where($column, 'like', '%data:image/%')->orderBy('id');

        if ($slug !== null) {
            if (! Schema::hasColumn($table, 'slug')) {
                return [];
            }

            $query->where('slug', $slug);
        }

        return $query->pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    private function bytes(int $bytes): string
    {
        return $bytes >= 1048576 ? number_format($bytes / 1048576, 2) . ' MB' : number_format($bytes / 1024, 1) . ' KB';
    }

    private function memoryLimitBytes(): int
    {
        $limit = trim((string) ini_get('memory_limit'));

        if ($limit === '-1') {
            return -1;
        }

        $number = (int) $limit;

        return match (strtolower(substr($limit, -1))) {
            'g' => $number * 1073741824,
            'm' => $number * 1048576,
            'k' => $number * 1024,
            default => $number,
        };
    }
}
