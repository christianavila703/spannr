<?php

namespace App\Support;

use GdImage;

/**
 * The few GD operations the image clean-up code shares (DEV-3): decode, apply EXIF orientation, shrink without
 * upscaling, detect real transparency, and encode. GD only; no other image library.
 */
final class GdImageTools
{
    public const JPEG_QUALITY = 82;
    public const MAX_WIDTH = 1600;

    /** Above this many pixels an image is not decoded at all (a decompression bomb would exhaust memory). */
    public const MAX_PIXELS = 40000000;

    /**
     * Decode image bytes into a true colour image that keeps its alpha channel.
     *
     * @return array{image: GdImage, width: int, height: int, type: int}|null null when the bytes are not a usable image
     */
    public static function decode(string $bytes): ?array
    {
        $info = @getimagesizefromstring($bytes);

        if (! $info || $info[0] < 1 || $info[1] < 1 || $info[0] * $info[1] > self::MAX_PIXELS) {
            return null;
        }

        if (! in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP], true)) {
            return null;
        }

        if (self::isAnimated($bytes, $info[2])) {
            return null;
        }

        $image = @imagecreatefromstring($bytes);

        if (! $image instanceof GdImage) {
            return null;
        }

        if (! imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);

        return ['image' => $image, 'width' => imagesx($image), 'height' => imagesy($image), 'type' => $info[2]];
    }

    /** GD would flatten an animation to one frame, so animated GIF and WebP files are left alone. */
    private static function isAnimated(string $bytes, int $type): bool
    {
        return match ($type) {
            IMAGETYPE_GIF => preg_match_all('/\x00\x21\xF9\x04/', $bytes) > 1,
            IMAGETYPE_WEBP => str_contains(substr($bytes, 0, 65536), 'ANIM'),
            default => false,
        };
    }

    /** Turn a JPEG upright from its EXIF orientation tag (when the exif extension is there). */
    public static function applyOrientation(GdImage $image, string $jpegBytes): GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $jpegBytes);
        rewind($stream);
        $exif = @exif_read_data($stream);
        fclose($stream);

        $angle = match ((int) ($exif['Orientation'] ?? 1)) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($angle === 0) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);

        if (! $rotated instanceof GdImage) {
            return $image;
        }

        imagealphablending($rotated, false);
        imagesavealpha($rotated, true);

        return $rotated;
    }

    /** Scale down to at most $maxWidth wide, keeping the proportions and the alpha channel. Never scales up. */
    public static function shrink(GdImage $image, int $maxWidth): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);

        if ($width <= $maxWidth) {
            return $image;
        }

        $newWidth = $maxWidth;
        $newHeight = max(1, (int) round($height * $maxWidth / $width));

        $scaled = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($scaled, false);
        imagesavealpha($scaled, true);
        imagefill($scaled, 0, 0, imagecolorallocatealpha($scaled, 0, 0, 0, 127));
        imagecopyresampled($scaled, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return $scaled;
    }

    /** True when at least one pixel is not fully opaque. */
    public static function hasTransparency(GdImage $image): bool
    {
        $width = imagesx($image);
        $height = imagesy($image);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if ((imagecolorat($image, $x, $y) >> 24) & 0x7F) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function jpeg(GdImage $image, int $quality = self::JPEG_QUALITY): string
    {
        imageinterlace($image, true);

        ob_start();
        imagejpeg($image, null, $quality);

        return (string) ob_get_clean();
    }

    /** PNG at maximum compression, alpha kept. */
    public static function png(GdImage $image): string
    {
        imagealphablending($image, false);
        imagesavealpha($image, true);

        ob_start();
        imagepng($image, null, 9);

        return (string) ob_get_clean();
    }
}
