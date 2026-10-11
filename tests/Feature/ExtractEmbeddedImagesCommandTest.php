<?php

namespace Tests\Feature;

use App\Support\EmbeddedImageExtractor;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Concerns\MakesGdImages;
use Tests\TestCase;

/**
 * DEV-3. The content:extract-embedded-images command.
 * In-memory SQLite and faked storage: no real database and no real file is touched.
 */
class ExtractEmbeddedImagesCommandTest extends TestCase
{
    use MakesGdImages;

    private const OLD_STAMP = '2020-01-01 00:00:00';

    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->longText('sidebar')->nullable();
            $table->longText('sources')->nullable();
            $table->date('content_updated_at')->nullable();
            $table->timestamps();
        });
        Schema::create('glossary', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->longText('content')->nullable();
            $table->timestamps();
        });

        Storage::fake('public');
        Storage::fake('local');
    }

    /** An article whose body holds one big pasted image. Returns [id, original content, png bytes]. */
    private function article(string $slug, ?string $content = null, array $extra = []): array
    {
        $png = $this->pngBytes($this->noiseImage(700, 500, crc32($slug) % 1000), 1);
        $content ??= '<p>Intro for ' . $slug . '</p><p><img alt="Chart" class="w-100" src="' . $this->dataUri($png) . '"></p><p>End</p>';

        $id = DB::table('articles')->insertGetId(array_merge([
            'name' => 'Name ' . $slug, 'slug' => $slug, 'content' => $content, 'sidebar' => '<p>Sidebar</p>', 'sources' => '<p>Sources</p>',
            'content_updated_at' => '2025-05-05', 'created_at' => self::OLD_STAMP, 'updated_at' => self::OLD_STAMP,
        ], $extra));

        return [$id, $content, $png];
    }

    private function row(int $id): object
    {
        return DB::table('articles')->find($id);
    }

    private function backups(): array
    {
        return Storage::disk('local')->allFiles('embedded-image-backups');
    }

    public function test_a_dry_run_writes_no_file_and_changes_no_row_and_reports_the_saving(): void
    {
        [$id, $original] = $this->article('heavy-article');

        $this->artisan('content:extract-embedded-images')
            ->expectsOutputToContain('DRY RUN')
            ->expectsOutputToContain('articles #' . $id . ' heavy-article content: 1 image(s)')
            ->expectsOutputToContain('1 column(s) with embedded images. HTML')
            ->assertExitCode(0);

        $this->assertSame([], Storage::disk('public')->allFiles(), 'no image written');
        $this->assertSame([], Storage::disk('local')->allFiles(), 'no backup written');
        $row = $this->row($id);
        $this->assertSame($original, $row->content);
        $this->assertSame(self::OLD_STAMP, $row->updated_at);
    }

    public function test_the_dry_run_report_shows_how_much_would_be_saved(): void
    {
        [, $original] = $this->article('heavy-article');

        \Illuminate\Support\Facades\Artisan::call('content:extract-embedded-images');
        $output = \Illuminate\Support\Facades\Artisan::output();

        $this->assertMatchesRegularExpression('/HTML ([\d.]+) MB before, about ([\d.]+) KB after: ([\d.]+) MB saved\./', $output);
        preg_match('/before, about ([\d.]+) KB after/', $output, $after);
        $this->assertLessThan(2, (float) $after[1], 'the page text shrinks to a couple of hundred bytes');
        preg_match('/HTML ([\d.]+) MB before/', $output, $before);
        $this->assertEqualsWithDelta(strlen($original) / 1048576, (float) $before[1], 0.01, 'the before figure is the real column size');
    }

    public function test_apply_updates_only_the_column_with_images_and_leaves_the_timestamps_alone(): void
    {
        [$id, $original, $png] = $this->article('heavy-article');

        $this->artisan('content:extract-embedded-images', ['--apply' => true])
            ->expectsOutputToContain('APPLYING')
            ->assertExitCode(0);

        $row = $this->row($id);
        $path = 'images/articles/embedded/' . substr(sha1($png), 0, 12) . '.jpg';
        Storage::disk('public')->assertExists($path);
        $this->assertSame(
            '<p>Intro for heavy-article</p><p><img alt="Chart" class="w-100" src="' . Storage::disk('public')->url($path) . '" width="700" height="500" loading="lazy"></p><p>End</p>',
            $row->content,
        );
        $this->assertSame('<p>Sidebar</p>', $row->sidebar);
        $this->assertSame('<p>Sources</p>', $row->sources);
        $this->assertSame('Name heavy-article', $row->name);
        $this->assertSame(self::OLD_STAMP, $row->updated_at, 'updated_at is not touched');
        $this->assertSame(self::OLD_STAMP, $row->created_at);
        $this->assertSame('2025-05-05', substr($row->content_updated_at, 0, 10), 'content_updated_at is not touched');
    }

    public function test_apply_writes_a_backup_holding_the_exact_original_before_changing_the_row(): void
    {
        [$id, $original] = $this->article('heavy-article');

        $this->artisan('content:extract-embedded-images', ['--apply' => true])->assertExitCode(0);

        $backups = $this->backups();
        $this->assertCount(1, $backups);
        $this->assertMatchesRegularExpression('#^embedded-image-backups/articles-' . $id . '-content-\d{8}-\d{6}\.html$#', $backups[0]);
        $this->assertSame($original, Storage::disk('local')->get($backups[0]), 'the exact original text');
        $this->assertSame([], preg_grep('#^embedded-image-backups#', Storage::disk('public')->allFiles()) ?: [], 'never on the public disk');
    }

    public function test_only_limits_the_run_to_one_slug(): void
    {
        [$a, $originalA] = $this->article('first-article');
        [$b, $originalB] = $this->article('second-article');

        $this->artisan('content:extract-embedded-images', ['--apply' => true, '--only' => 'second-article'])->assertExitCode(0);

        $this->assertSame($originalA, $this->row($a)->content, 'the other article is untouched');
        $this->assertStringNotContainsString('data:image', $this->row($b)->content);
        $this->assertCount(1, $this->backups());
    }

    public function test_a_second_apply_run_finds_nothing(): void
    {
        $this->article('heavy-article');
        $this->artisan('content:extract-embedded-images', ['--apply' => true])->assertExitCode(0);
        $contentAfterFirst = DB::table('articles')->value('content');
        $backupsAfterFirst = $this->backups();
        $filesAfterFirst = Storage::disk('public')->allFiles();

        $this->artisan('content:extract-embedded-images', ['--apply' => true])
            ->expectsOutputToContain('0 column(s) with embedded images')
            ->assertExitCode(0);

        $this->assertSame($contentAfterFirst, DB::table('articles')->value('content'));
        $this->assertSame($backupsAfterFirst, $this->backups(), 'no new backup');
        $this->assertSame($filesAfterFirst, Storage::disk('public')->allFiles());
    }

    public function test_a_record_with_only_an_svg_data_uri_is_not_touched_or_backed_up(): void
    {
        $content = '<p><img src="data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg"/>') . '"></p>';
        [$id] = $this->article('svg-article', $content);

        $this->artisan('content:extract-embedded-images', ['--apply' => true])
            ->expectsOutputToContain('0 column(s) with embedded images')
            ->assertExitCode(0);

        $this->assertSame($content, $this->row($id)->content);
        $this->assertSame([], $this->backups());
    }

    public function test_images_in_the_sidebar_and_in_another_table_are_found_too(): void
    {
        $png = $this->pngBytes($this->noiseImage(300, 200, 21), 1);
        [$id] = $this->article('sidebar-article', '<p>Plain body</p>', ['sidebar' => '<img src="' . $this->dataUri($png) . '">']);
        $glossaryId = DB::table('glossary')->insertGetId([
            'name' => 'NAD', 'slug' => 'nad', 'content' => '<img src="' . $this->dataUri($png) . '">', 'created_at' => self::OLD_STAMP, 'updated_at' => self::OLD_STAMP,
        ]);

        $this->artisan('content:extract-embedded-images', ['--apply' => true])
            ->expectsOutputToContain('2 column(s) with embedded images')
            ->assertExitCode(0);

        $this->assertStringNotContainsString('data:image', $this->row($id)->sidebar);
        $this->assertSame('<p>Plain body</p>', $this->row($id)->content);
        $this->assertStringNotContainsString('data:image', DB::table('glossary')->find($glossaryId)->content);
        $this->assertSame(self::OLD_STAMP, DB::table('glossary')->find($glossaryId)->updated_at);
        $this->assertCount(2, $this->backups());
        $this->assertCount(1, Storage::disk('public')->allFiles(), 'the same image in two places is one file');
    }

    public function test_the_table_option_limits_the_run_and_rejects_an_unknown_table(): void
    {
        [$id, $original] = $this->article('heavy-article');

        $this->artisan('content:extract-embedded-images', ['--apply' => true, '--table' => 'glossary'])
            ->expectsOutputToContain('0 column(s) with embedded images')
            ->assertExitCode(0);
        $this->assertSame($original, $this->row($id)->content, 'articles were not in scope');

        $this->artisan('content:extract-embedded-images', ['--table' => 'users'])
            ->expectsOutputToContain('Unknown table "users"')
            ->assertExitCode(1);
    }

    public function test_a_tables_that_does_not_exist_is_skipped_without_stopping(): void
    {
        $this->article('heavy-article');

        // listings, organizations, jobs and content are not created in this test database.
        $this->artisan('content:extract-embedded-images')
            ->expectsOutputToContain('Skipping listings: no such table.')
            ->expectsOutputToContain('1 column(s) with embedded images')
            ->assertExitCode(0);
    }

    public function test_one_failing_record_is_reported_and_the_others_still_run(): void
    {
        [$bad, $badOriginal] = $this->article('first-article');
        [$good] = $this->article('second-article');

        // An extractor that cannot handle the first article's text.
        $this->app->bind(EmbeddedImageExtractor::class, fn () => new class extends EmbeddedImageExtractor {
            public function extract(string $html): array
            {
                if (str_contains($html, 'Intro for first-article')) {
                    throw new \RuntimeException('boom');
                }

                return parent::extract($html);
            }
        });

        $this->artisan('content:extract-embedded-images', ['--apply' => true])
            ->expectsOutputToContain('FAILED articles #' . $bad . ' content: boom')
            ->expectsOutputToContain('1 record(s) failed and were left unchanged.')
            ->assertExitCode(1);

        $this->assertSame($badOriginal, $this->row($bad)->content, 'left exactly as it was');
        $this->assertStringNotContainsString('data:image', $this->row($good)->content, 'the next record still ran');
        $this->assertCount(1, $this->backups());
    }
}
