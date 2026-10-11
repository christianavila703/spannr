<?php

namespace Tests\Feature;

use App\Support\EmbeddedImageExtractor;
use App\Support\GdImageTools;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Concerns\MakesGdImages;
use Tests\TestCase;

/**
 * DEV-3. App\Support\EmbeddedImageExtractor turns base64 images inside HTML into files.
 * Storage is faked and the test images are made with GD: no real file is read or written.
 */
class EmbeddedImageExtractorTest extends TestCase
{
    use MakesGdImages;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    /** With no argument this is the extractor as the command builds it, so the default width is what is tested. */
    private function extractor(?int $maxWidth = null): EmbeddedImageExtractor
    {
        return $maxWidth === null ? new EmbeddedImageExtractor('public') : new EmbeddedImageExtractor('public', $maxWidth);
    }

    private function url(string $path): string
    {
        return Storage::disk('public')->url($path);
    }

    /** @return array<int, string> every file on the fake public disk */
    private function files(): array
    {
        return Storage::disk('public')->allFiles();
    }

    public function test_a_data_uri_becomes_a_file_and_the_src_is_its_public_url(): void
    {
        $png = $this->pngBytes($this->noiseImage(600, 400));
        $html = '<p>Intro</p><p><img src="' . $this->dataUri($png) . '"></p><p>Outro</p>';

        $result = $this->extractor()->process($html);

        $path = 'images/articles/embedded/' . substr(sha1($png), 0, 12) . '.jpg';
        $this->assertSame(1, $result['count']);
        Storage::disk('public')->assertExists($path);
        $this->assertSame('<p>Intro</p><p><img src="' . $this->url($path) . '" width="600" height="400" loading="lazy"></p><p>Outro</p>', $result['html']);
        $this->assertStringNotContainsString('data:image', $result['html']);
        $this->assertLessThan(strlen($html) / 10, strlen($result['html']), 'the HTML is a fraction of its old size');
    }

    public function test_alt_class_style_and_the_surrounding_html_are_kept_byte_for_byte(): void
    {
        $png = $this->pngBytes($this->noiseImage(300, 200));
        $before = "<h2 class=\"h3\">Title &amp; more</h2>\r\n<ul><li>One</li></ul>\n\t<p style=\"margin:0\">Text <strong>bold</strong></p>\n";
        $after = "\n<table><tr><td>cell</td></tr></table><!-- keep me --><iframe src=\"https://example.com/x\"></iframe>";
        $tag = '<img alt="Sleep &amp; recovery chart" class="w-100 rounded" style="border:1px solid #000" data-id="7" src="' . $this->dataUri($png) . '" title="T">';

        $result = $this->extractor()->process($before . $tag . $after);

        $path = 'images/articles/embedded/' . substr(sha1($png), 0, 12) . '.jpg';
        $expectedTag = '<img alt="Sleep &amp; recovery chart" class="w-100 rounded" style="border:1px solid #000" data-id="7" src="'
            . $this->url($path) . '" title="T" width="300" height="200" loading="lazy">';
        $this->assertSame($before . $expectedTag . $after, $result['html']);
    }

    public function test_a_wide_image_is_shrunk_to_1600_and_a_small_one_is_never_enlarged(): void
    {
        $wide = $this->pngBytes($this->noiseImage(1800, 300, 2));
        $small = $this->pngBytes($this->noiseImage(800, 200, 3));

        $result = $this->extractor()->process('<img src="' . $this->dataUri($wide) . '"><img src="' . $this->dataUri($small) . '">');

        $wideFile = Storage::disk('public')->get('images/articles/embedded/' . substr(sha1($wide), 0, 12) . '.jpg');
        $smallFile = Storage::disk('public')->get('images/articles/embedded/' . substr(sha1($small), 0, 12) . '.jpg');
        $this->assertSame([1600, 267], array_slice($this->sizeOf($wideFile), 0, 2), '1800x300 scaled to 1600 wide, same proportions');
        $this->assertSame([800, 200], array_slice($this->sizeOf($smallFile), 0, 2), 'no upscaling');
        $this->assertStringContainsString('width="1600" height="267"', $result['html']);
        $this->assertStringContainsString('width="800" height="200"', $result['html']);
    }

    public function test_the_max_width_can_be_changed(): void
    {
        $png = $this->pngBytes($this->noiseImage(900, 100));

        $this->extractor(500)->process('<img src="' . $this->dataUri($png) . '">');

        $file = Storage::disk('public')->get('images/articles/embedded/' . substr(sha1($png), 0, 12) . '.jpg');
        $this->assertSame(500, $this->sizeOf($file)[0]);
    }

    public function test_a_transparent_png_stays_a_png_and_an_opaque_one_becomes_a_jpeg(): void
    {
        $transparent = $this->pngBytes($this->transparentImage(400, 300));
        $opaque = $this->pngBytes($this->noiseImage(400, 300, 4));

        $result = $this->extractor()->process('<img src="' . $this->dataUri($transparent) . '"><img src="' . $this->dataUri($opaque) . '">');

        $transparentPath = 'images/articles/embedded/' . substr(sha1($transparent), 0, 12) . '.png';
        $opaquePath = 'images/articles/embedded/' . substr(sha1($opaque), 0, 12) . '.jpg';
        Storage::disk('public')->assertExists([$transparentPath, $opaquePath]);
        $this->assertCount(2, $this->files());

        $this->assertSame(IMAGETYPE_PNG, $this->sizeOf(Storage::disk('public')->get($transparentPath))[2]);
        $decoded = GdImageTools::decode(Storage::disk('public')->get($transparentPath));
        $this->assertTrue(GdImageTools::hasTransparency($decoded['image']), 'the see-through part survived');
        $this->assertSame(IMAGETYPE_JPEG, $this->sizeOf(Storage::disk('public')->get($opaquePath))[2]);
        $this->assertStringContainsString($this->url($transparentPath), $result['html']);
        $this->assertStringContainsString($this->url($opaquePath), $result['html']);
    }

    public function test_a_png_with_an_alpha_channel_that_is_fully_opaque_becomes_a_jpeg(): void
    {
        $image = $this->noiseImage(200, 200, 5);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        $png = $this->pngBytes($image);

        $this->extractor()->process('<img src="' . $this->dataUri($png) . '">');

        Storage::disk('public')->assertExists('images/articles/embedded/' . substr(sha1($png), 0, 12) . '.jpg');
    }

    public function test_the_same_image_twice_is_one_file(): void
    {
        $png = $this->pngBytes($this->noiseImage(300, 300, 6));
        $uri = $this->dataUri($png);

        $result = $this->extractor()->process('<p><img src="' . $uri . '" alt="one"></p><p><img alt="two" src=\'' . $uri . '\'></p>');

        $this->assertSame(2, $result['count']);
        $this->assertCount(1, $this->files());
        $this->assertSame(2, substr_count($result['html'], $this->url($this->files()[0])));
    }

    public function test_an_svg_data_uri_an_external_src_and_bad_data_are_left_alone(): void
    {
        $svg = '<img src="data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg"/>') . '" alt="logo">';
        $svgPlain = '<img src="data:image/svg+xml;utf8,%3Csvg%3E%3C/svg%3E">';
        $external = '<img src="https://example.com/photo.jpg" alt="x">';
        $relative = '<img src="/storage/images/articles/a.jpg">';
        $badBase64 = '<img src="data:image/png;base64,@@@not base64@@@" alt="bad">';
        $notAnImage = '<img src="data:image/png;base64,' . base64_encode('this is plain text, not an image') . '" alt="text">';
        $html = '<p>' . $svg . $svgPlain . $external . $relative . $badBase64 . $notAnImage . '</p>';

        $result = $this->extractor()->process($html);

        $this->assertSame($html, $result['html']);
        $this->assertSame(0, $result['count']);
        $this->assertSame(2, $result['skipped'], 'the bad base64 and the text that is not an image');
        $this->assertSame([], $this->files());
    }

    public function test_a_single_quoted_src_is_converted_and_keeps_its_quotes(): void
    {
        $png = $this->pngBytes($this->noiseImage(120, 80, 8));
        $path = 'images/articles/embedded/' . substr(sha1($png), 0, 12) . '.jpg';

        $result = $this->extractor()->process("<img class='x' src='" . $this->dataUri($png) . "'>");

        $this->assertSame("<img class='x' src='" . $this->url($path) . "' width=\"120\" height=\"80\" loading=\"lazy\">", $result['html']);
    }

    public function test_existing_width_height_and_loading_are_not_duplicated_or_replaced(): void
    {
        $png = $this->pngBytes($this->noiseImage(400, 200, 9));
        $path = 'images/articles/embedded/' . substr(sha1($png), 0, 12) . '.jpg';
        $uri = $this->dataUri($png);

        $full = $this->extractor()->extract('<img width="300" height="150" loading="eager" src="' . $uri . '">')['html'];
        $onlyWidth = $this->extractor()->extract('<img width="300" src="' . $uri . '">')['html'];

        $this->assertSame('<img width="300" height="150" loading="eager" src="' . $this->url($path) . '">', $full);
        $this->assertSame('<img width="300" src="' . $this->url($path) . '" loading="lazy">', $onlyWidth, 'a lone width is not paired with a height that could stretch the picture');
    }

    public function test_a_self_closing_tag_and_a_greater_than_sign_in_alt_text_are_handled(): void
    {
        $png = $this->pngBytes($this->noiseImage(100, 100, 10));
        $path = 'images/articles/embedded/' . substr(sha1($png), 0, 12) . '.jpg';

        $result = $this->extractor()->extract('<img alt="a > b" src="' . $this->dataUri($png) . '" />');

        $this->assertSame('<img alt="a > b" src="' . $this->url($path) . '" width="100" height="100" loading="lazy" />', $result['html']);
    }

    public function test_data_src_is_not_mistaken_for_src(): void
    {
        $png = $this->pngBytes($this->noiseImage(100, 100, 11));
        $html = '<img data-src="' . $this->dataUri($png) . '" src="https://example.com/a.jpg">';

        $this->assertSame($html, $this->extractor()->extract($html)['html']);
    }

    public function test_extract_alone_writes_nothing_and_store_writes_the_files(): void
    {
        $png = $this->pngBytes($this->noiseImage(200, 100, 12));
        $extractor = $this->extractor();

        $result = $extractor->extract('<img src="' . $this->dataUri($png) . '">');

        $this->assertSame([], $this->files(), 'extract is pure');
        $this->assertCount(1, $result['files']);

        $extractor->store($result);

        $this->assertCount(1, $this->files());
    }

    public function test_running_it_on_its_own_output_changes_nothing(): void
    {
        $png = $this->pngBytes($this->noiseImage(500, 300, 13));
        $extractor = $this->extractor();

        $first = $extractor->process('<p>x</p><img alt="a" src="' . $this->dataUri($png) . '"><p>y</p>');
        $filesAfterFirst = $this->files();
        $second = $extractor->process($first['html']);

        $this->assertSame($first['html'], $second['html']);
        $this->assertSame(0, $second['count']);
        $this->assertSame($filesAfterFirst, $this->files());
    }

    public function test_text_with_no_images_is_returned_unchanged_and_fast(): void
    {
        $html = '<p>No pictures here, and a mention of data:image in plain text.</p>';

        $this->assertSame($html, $this->extractor()->process($html)['html']);
        $this->assertSame('', $this->extractor()->process('')['html']);
    }

    public function test_a_tag_holding_several_megabytes_is_handled(): void
    {
        // About 3.4 MB of PNG, like the pasted images on the live site (random pixels do not compress).
        $png = $this->pngBytes($this->noiseImage(1200, 900, 14), 1);
        $this->assertGreaterThan(2500000, strlen($png));

        $result = $this->extractor()->process('<p><img src="' . $this->dataUri($png) . '"></p>');

        $this->assertSame(1, $result['count']);
        $this->assertLessThan(500, strlen($result['html']));
    }
}
