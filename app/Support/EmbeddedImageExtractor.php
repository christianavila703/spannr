<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Moves images that were pasted into the CMS editor and stored inside rich text as base64 data URIs out into real files (DEV-3).
 *
 * It works on the HTML as a string, never through a DOM parser, so everything that is not an embedded image stays byte for
 * byte identical. For each <img> whose src is data:image/(png|jpeg|jpg|gif|webp);base64,... it decodes the image, shrinks it
 * to at most 1600 pixels wide (never upscaling), re-encodes it (JPEG quality 82; PNG only when it has real transparency),
 * stores it on the public disk as images/articles/embedded/{first 12 of sha1 of the original bytes}.{jpg|png}, and replaces
 * only the src value with the file's public URL. The same image used twice becomes one file. Other attributes are kept; width
 * and height are added when the tag has neither, and loading="lazy" when it has no loading attribute.
 *
 * Untouched: data:image/svg+xml, external URLs, invalid base64, anything GD cannot decode, animated GIF and WebP.
 * Running it on its own output changes nothing, because no data URI is left to find.
 *
 * extract() is pure (no file is written); store() writes the files; process() does both.
 */
class EmbeddedImageExtractor
{
    public const DIRECTORY = 'images/articles/embedded';

    // An <img> tag, quoted attribute values included (so a ">" inside alt="..." does not end it). Possessive, so a tag
    // holding several megabytes of base64 costs no backtracking.
    private const TAG_PATTERN = '/<img\b(?:[^>"\']++|"[^"]*+"|\'[^\']*+\')*+>/i';

    private const SRC_PATTERN = '/(\ssrc\s*=\s*)(["\'])data:image\/(?:png|jpe?g|gif|webp);base64,([^"\']*+)\2/i';

    public function __construct(
        private readonly string $disk = 'public',
        private readonly int $maxWidth = GdImageTools::MAX_WIDTH,
    ) {
    }

    /**
     * @return array{html: string, count: int, skipped: int, files: array<string, string>}
     *         html: the converted text; count: images replaced; skipped: data URIs left alone because they could not be
     *         used; files: path on the disk => file bytes, one entry per distinct image
     */
    public function extract(string $html): array
    {
        if (stripos($html, 'data:image/') === false) {
            return ['html' => $html, 'count' => 0, 'skipped' => 0, 'files' => []];
        }

        $files = [];
        $count = 0;
        $skipped = 0;

        $converted = preg_replace_callback(self::TAG_PATTERN, function (array $match) use (&$files, &$count, &$skipped) {
            $tag = $match[0];

            if (stripos($tag, 'data:image/') === false || ! preg_match(self::SRC_PATTERN, $tag, $src, PREG_OFFSET_CAPTURE)) {
                return $tag;
            }

            $image = $this->convert($src[3][0]);

            if ($image === null) {
                $skipped++;

                return $tag;
            }

            $files[$image['path']] = $image['bytes'];
            $quote = $src[2][0];
            $url = Storage::disk($this->disk)->url($image['path']);

            $tag = substr_replace($tag, $src[1][0] . $quote . $url . $quote, $src[0][1], strlen($src[0][0]));
            $count++;

            return $this->addMissingAttributes($tag, $image['width'], $image['height']);
        }, $html);

        if ($converted === null) {
            throw new RuntimeException('Could not scan the HTML for images: ' . preg_last_error_msg());
        }

        return ['html' => $converted, 'count' => $count, 'skipped' => $skipped, 'files' => $files];
    }

    /** Write the files from extract() to the disk. A file that already exists (same image, same name) is left as it is. */
    public function store(array $result): void
    {
        $disk = Storage::disk($this->disk);

        foreach ($result['files'] as $path => $bytes) {
            if (! $disk->exists($path) && ! $disk->put($path, $bytes)) {
                throw new RuntimeException('Could not write ' . $path);
            }
        }
    }

    /** extract() and store() in one go. */
    public function process(string $html): array
    {
        $result = $this->extract($html);
        $this->store($result);

        return $result;
    }

    /**
     * @return array{path: string, bytes: string, width: int, height: int}|null null when this image cannot be used
     */
    private function convert(string $base64): ?array
    {
        $original = base64_decode(preg_replace('/\s+/', '', $base64), true);

        if ($original === false || $original === '') {
            return null;
        }

        $decoded = GdImageTools::decode($original);

        if ($decoded === null) {
            return null;
        }

        $image = GdImageTools::shrink($decoded['image'], $this->maxWidth);
        $resized = $image !== $decoded['image'];
        $transparent = GdImageTools::hasTransparency($image);

        $extension = $transparent ? 'png' : 'jpg';
        $bytes = $transparent ? GdImageTools::png($image) : GdImageTools::jpeg($image);

        // An image that needed no resizing and is already in the target format is kept as it came if re-encoding made it bigger.
        $sameFormat = ($extension === 'jpg' && $decoded['type'] === IMAGETYPE_JPEG)
            || ($extension === 'png' && $decoded['type'] === IMAGETYPE_PNG);

        if (! $resized && $sameFormat && strlen($original) <= strlen($bytes)) {
            $bytes = $original;
        }

        return [
            'path' => self::DIRECTORY . '/' . substr(sha1($original), 0, 12) . '.' . $extension,
            'bytes' => $bytes,
            'width' => imagesx($image),
            'height' => imagesy($image),
        ];
    }

    /**
     * width and height go in only when the tag has neither (adding one next to an existing other could stretch the
     * picture); loading="lazy" only when the tag has no loading attribute.
     */
    private function addMissingAttributes(string $tag, int $width, int $height): string
    {
        $add = [];

        if (! preg_match('/\swidth\s*=/i', $tag) && ! preg_match('/\sheight\s*=/i', $tag)) {
            $add[] = 'width="' . $width . '"';
            $add[] = 'height="' . $height . '"';
        }

        if (! preg_match('/\sloading\s*=/i', $tag)) {
            $add[] = 'loading="lazy"';
        }

        if ($add === []) {
            return $tag;
        }

        $attributes = implode(' ', $add);

        if (preg_match('/\s*\/>$/', $tag, $end)) {
            return substr($tag, 0, -strlen($end[0])) . ' ' . $attributes . ' />';
        }

        return substr($tag, 0, -1) . ' ' . $attributes . '>';
    }
}
