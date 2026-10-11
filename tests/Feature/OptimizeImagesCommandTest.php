<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\Feature\Concerns\MakesGdImages;
use Tests\TestCase;

/**
 * DEV-3. The images:optimize command. Storage is faked and the pictures are made with GD:
 * no real file under storage/ is ever read or changed.
 */
class OptimizeImagesCommandTest extends TestCase
{
    use MakesGdImages;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Storage::fake('local');
    }

    /** A large photo-like JPEG (random pixels at quality 100 shrink a lot at quality 82). */
    private function bigJpeg(string $path, int $width = 900, int $height = 600): string
    {
        $bytes = $this->jpegBytes($this->noiseImage($width, $height, 31), 100);
        Storage::disk('public')->put($path, $bytes);

        return $bytes;
    }

    public function test_a_dry_run_changes_nothing_and_reports_the_estimate(): void
    {
        $bytes = $this->bigJpeg('images/articles/hevolution-hf-age.jpg');

        $this->artisan('images:optimize', ['--min-kb' => 100])
            ->expectsOutputToContain('DRY RUN')
            ->expectsOutputToContain('images/articles/hevolution-hf-age.jpg:')
            ->expectsOutputToContain('1 file(s) would shrink')
            ->assertExitCode(0);

        $this->assertSame($bytes, Storage::disk('public')->get('images/articles/hevolution-hf-age.jpg'));
        $this->assertSame([], Storage::disk('local')->allFiles(), 'no backup in a dry run');
    }

    public function test_apply_shrinks_the_file_at_the_same_path_and_keeps_the_original_in_image_originals(): void
    {
        $original = $this->bigJpeg('images/articles/fda-restricts-nmn.jpg');

        $this->artisan('images:optimize', ['--apply' => true, '--min-kb' => 100])
            ->expectsOutputToContain('APPLYING')
            ->expectsOutputToContain('1 file(s) shrunk')
            ->assertExitCode(0);

        $new = Storage::disk('public')->get('images/articles/fda-restricts-nmn.jpg');
        $this->assertLessThan(strlen($original) * 0.9, strlen($new), 'at least 10 percent smaller');
        $this->assertSame(IMAGETYPE_JPEG, $this->sizeOf($new)[2], 'still a JPEG under the same name');
        $this->assertSame([900, 600], array_slice($this->sizeOf($new), 0, 2), 'no resize needed, so the same size in pixels');
        $this->assertSame(['images/articles/fda-restricts-nmn.jpg'], Storage::disk('public')->allFiles(), 'no other file appeared');
        Storage::disk('local')->assertExists('image-originals/images/articles/fda-restricts-nmn.jpg');
        $this->assertSame($original, Storage::disk('local')->get('image-originals/images/articles/fda-restricts-nmn.jpg'));
    }

    public function test_a_wider_image_is_resized_to_the_max_width_and_a_narrower_one_is_not_enlarged(): void
    {
        $this->bigJpeg('images/articles/wide.jpg', 2000, 400);
        $this->bigJpeg('images/articles/narrow.jpg', 700, 500);

        $this->artisan('images:optimize', ['--apply' => true, '--min-kb' => 100])->assertExitCode(0);

        $this->assertSame([1600, 320], array_slice($this->sizeOf(Storage::disk('public')->get('images/articles/wide.jpg')), 0, 2));
        $this->assertSame([700, 500], array_slice($this->sizeOf(Storage::disk('public')->get('images/articles/narrow.jpg')), 0, 2));
    }

    public function test_the_max_width_option_is_used(): void
    {
        $this->bigJpeg('images/articles/wide.jpg', 1000, 300);

        $this->artisan('images:optimize', ['--apply' => true, '--min-kb' => 100, '--max-width' => 800])->assertExitCode(0);

        $this->assertSame(800, $this->sizeOf(Storage::disk('public')->get('images/articles/wide.jpg'))[0]);
    }

    public function test_files_below_the_threshold_are_untouched(): void
    {
        $small = $this->jpegBytes($this->noiseImage(100, 100), 100);
        Storage::disk('public')->put('images/articles/small.jpg', $small);
        $this->assertLessThan(400 * 1024, strlen($small));

        $this->artisan('images:optimize', ['--apply' => true])->assertExitCode(0);

        $this->assertSame($small, Storage::disk('public')->get('images/articles/small.jpg'));
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_a_file_that_would_not_shrink_ten_percent_is_untouched(): void
    {
        // A picture the command has already compressed once: compressing it again gains next to nothing.
        $lean = \App\Support\GdImageTools::jpeg($this->noiseImage(900, 600, 32));
        Storage::disk('public')->put('images/articles/lean.jpg', $lean);

        $this->artisan('images:optimize', ['--apply' => true, '--min-kb' => 0])
            ->expectsOutputToContain('skip images/articles/lean.jpg')
            ->assertExitCode(0);

        $this->assertSame($lean, Storage::disk('public')->get('images/articles/lean.jpg'));
        $this->assertSame([], Storage::disk('local')->allFiles(), 'no backup for a file that was not changed');
    }

    public function test_the_media_library_folders_and_other_file_types_are_never_scanned(): void
    {
        $media = $this->jpegBytes($this->noiseImage(900, 600, 5), 100);
        Storage::disk('public')->put('media/12/conversions/photo-medium.jpg', $media);
        Storage::disk('public')->put('12/photo.jpg', $media);
        Storage::disk('public')->put('images/articles/notes.txt', str_repeat('x', 600 * 1024));
        Storage::disk('public')->put('images/articles/logo.svg', str_repeat('x', 600 * 1024));

        $this->artisan('images:optimize', ['--apply' => true, '--min-kb' => 1])
            ->expectsOutputToContain('0 file(s) shrunk')
            ->assertExitCode(0);

        $this->assertSame($media, Storage::disk('public')->get('media/12/conversions/photo-medium.jpg'));
        $this->assertSame($media, Storage::disk('public')->get('12/photo.jpg'));
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_a_png_stays_a_png_under_the_same_name_and_keeps_its_transparency(): void
    {
        $original = $this->pngBytes($this->transparentImage(2000, 300), 1);
        Storage::disk('public')->put('images/articles/overlay.png', $original);

        $this->artisan('images:optimize', ['--apply' => true, '--min-kb' => 100])->assertExitCode(0);

        $new = Storage::disk('public')->get('images/articles/overlay.png');
        $this->assertSame(IMAGETYPE_PNG, $this->sizeOf($new)[2]);
        $this->assertSame(1600, $this->sizeOf($new)[0]);
        $decoded = \App\Support\GdImageTools::decode($new);
        $this->assertTrue(\App\Support\GdImageTools::hasTransparency($decoded['image']));
        $this->assertSame($original, Storage::disk('local')->get('image-originals/images/articles/overlay.png'));
    }

    public function test_a_second_apply_run_changes_nothing_and_keeps_the_first_original(): void
    {
        $original = $this->bigJpeg('images/articles/fda-restricts-nmn.jpg');
        $this->artisan('images:optimize', ['--apply' => true, '--min-kb' => 100])->assertExitCode(0);
        $afterFirst = Storage::disk('public')->get('images/articles/fda-restricts-nmn.jpg');

        $this->artisan('images:optimize', ['--apply' => true, '--min-kb' => 1])->assertExitCode(0);

        $this->assertSame($afterFirst, Storage::disk('public')->get('images/articles/fda-restricts-nmn.jpg'));
        $this->assertSame($original, Storage::disk('local')->get('image-originals/images/articles/fda-restricts-nmn.jpg'), 'the true original is never overwritten');
    }

    public function test_a_file_that_is_not_really_an_image_is_skipped(): void
    {
        Storage::disk('public')->put('images/articles/broken.jpg', str_repeat('not an image', 60000));

        $this->artisan('images:optimize', ['--apply' => true, '--min-kb' => 1])->assertExitCode(0);

        $this->assertSame(str_repeat('not an image', 60000), Storage::disk('public')->get('images/articles/broken.jpg'));
        $this->assertSame([], Storage::disk('local')->allFiles());
    }
}
