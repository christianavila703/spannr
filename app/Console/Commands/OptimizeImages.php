<?php

namespace App\Console\Commands;

use App\Support\GdImageTools;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * DEV-3. Shrinks oversized photos under images/ on the public disk (for example the article photos in images/articles).
 * The Spatie media-library folders are never scanned: only images/ is.
 *
 * Default is a dry run. With --apply each original is first copied to storage/app/image-originals (same relative path,
 * and never overwritten once it exists), then the file is overwritten in place, so its name and extension never change and
 * no URL breaks. A file is only replaced when the result is at least 10 percent smaller.
 */
class OptimizeImages extends Command
{
    public const BACKUP_DIRECTORY = 'image-originals';

    /** The new file must be at most this share of the old one (at least 10 percent smaller). */
    private const REQUIRED_RATIO = 0.9;

    protected $signature = 'images:optimize
        {--apply : Overwrite the files (after backing up each original). Without it this is a dry run}
        {--min-kb=400 : Only look at files larger than this many kilobytes}
        {--max-width=1600 : Shrink images wider than this many pixels (never enlarges)}';

    protected $description = 'Shrink oversized jpg and png files under images/ on the public disk (dry run unless --apply)';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');
        $minBytes = max(0, (int) $this->option('min-kb')) * 1024;
        $maxWidth = max(1, (int) $this->option('max-width'));

        if (ini_get('memory_limit') !== '-1' && $this->memoryLimitMegabytes() < 768) {
            ini_set('memory_limit', '768M');
        }

        $this->line($apply ? 'APPLYING changes.' : 'DRY RUN: nothing is written. Add --apply to make the changes.');

        $public = Storage::disk('public');
        $changed = 0;
        $failed = 0;
        $before = 0;
        $after = 0;

        foreach ($public->allFiles('images') as $path) {
            if (! preg_match('/\.(jpe?g|png)$/i', $path)) {
                continue;
            }

            $size = $public->size($path);

            if ($size <= $minBytes) {
                continue;
            }

            try {
                $bytes = (string) $public->get($path);
                $new = $this->reencode($bytes, $path, $maxWidth);
            } catch (Throwable $e) {
                $failed++;
                $this->error('FAILED ' . $path . ': ' . $e->getMessage());

                continue;
            }

            if ($new === null || strlen($new) > $size * self::REQUIRED_RATIO) {
                $this->line(sprintf('skip %s (%s): not at least 10 percent smaller', $path, $this->kb($size)));

                continue;
            }

            if ($apply) {
                try {
                    $backup = self::BACKUP_DIRECTORY . '/' . $path;

                    if (! Storage::disk('local')->exists($backup) && ! Storage::disk('local')->put($backup, $bytes)) {
                        throw new \RuntimeException('Could not write the backup ' . $backup);
                    }

                    $public->put($path, $new);
                } catch (Throwable $e) {
                    $failed++;
                    $this->error('FAILED ' . $path . ': ' . $e->getMessage());

                    continue;
                }
            }

            $changed++;
            $before += $size;
            $after += strlen($new);
            $this->line(sprintf('%s: %s -> %s%s', $path, $this->kb($size), $apply ? '' : 'about ', $this->kb(strlen($new))));
        }

        $this->newLine();
        $this->line(sprintf(
            '%d file(s) %s. %s before, %s after: %s saved.',
            $changed,
            $apply ? 'shrunk' : 'would shrink',
            $this->kb($before),
            $this->kb($after),
            $this->kb($before - $after),
        ));

        if ($failed > 0) {
            $this->error($failed . ' file(s) failed and were left unchanged.');

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /** The file re-encoded in its own format (so the name never changes), or null when it cannot be decoded. */
    private function reencode(string $bytes, string $path, int $maxWidth): ?string
    {
        $decoded = GdImageTools::decode($bytes);

        if ($decoded === null) {
            return null;
        }

        $image = $decoded['image'];

        if ($decoded['type'] === IMAGETYPE_JPEG) {
            $image = GdImageTools::applyOrientation($image, $bytes);
        }

        $image = GdImageTools::shrink($image, $maxWidth);

        $isPng = (bool) preg_match('/\.png$/i', $path);

        // A .png stays PNG (with its transparency if it has any), a .jpg stays JPEG.
        return $isPng ? GdImageTools::png($image) : GdImageTools::jpeg($image);
    }

    private function kb(int $bytes): string
    {
        return number_format($bytes / 1024, 1) . ' KB';
    }

    private function memoryLimitMegabytes(): int
    {
        $limit = trim((string) ini_get('memory_limit'));
        $number = (int) $limit;

        return match (strtolower(substr($limit, -1))) {
            'g' => $number * 1024,
            'k' => intdiv($number, 1024),
            'm' => $number,
            default => intdiv($number, 1048576),
        };
    }
}
