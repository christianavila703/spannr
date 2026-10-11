<?php

namespace Tests\Feature\Concerns;

use GdImage;

/**
 * Test images made with GD, so no test touches a real file.
 * "Noise" images are random pixels: they compress badly, which makes them large and easy to shrink.
 */
trait MakesGdImages
{
    protected function noiseImage(int $width, int $height, int $seed = 1): GdImage
    {
        mt_srand($seed);
        $image = imagecreatetruecolor($width, $height);

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                imagesetpixel($image, $x, $y, (mt_rand(0, 255) << 16) | (mt_rand(0, 255) << 8) | mt_rand(0, 255));
            }
        }

        return $image;
    }

    /** A smooth, fully opaque picture. */
    protected function gradientImage(int $width, int $height): GdImage
    {
        $image = imagecreatetruecolor($width, $height);

        for ($x = 0; $x < $width; $x++) {
            imageline($image, $x, 0, $x, $height, imagecolorallocate($image, (int) ($x * 255 / $width), 80, 160));
        }

        return $image;
    }

    /** A picture whose left half is see-through. */
    protected function transparentImage(int $width, int $height): GdImage
    {
        $image = $this->noiseImage($width, $height, 7);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagefilledrectangle($image, 0, 0, intdiv($width, 2), $height, imagecolorallocatealpha($image, 0, 0, 0, 127));

        return $image;
    }

    protected function pngBytes(GdImage $image, int $level = 6): string
    {
        imagealphablending($image, false);
        imagesavealpha($image, true);
        ob_start();
        imagepng($image, null, $level);

        return (string) ob_get_clean();
    }

    protected function jpegBytes(GdImage $image, int $quality = 95): string
    {
        ob_start();
        imagejpeg($image, null, $quality);

        return (string) ob_get_clean();
    }

    protected function dataUri(string $bytes, string $mime = 'image/png'): string
    {
        return 'data:' . $mime . ';base64,' . base64_encode($bytes);
    }

    /** @return array{0: int, 1: int, 2: int} width, height, image type of the bytes */
    protected function sizeOf(string $bytes): array
    {
        $info = getimagesizefromstring($bytes);

        return [$info[0], $info[1], $info[2]];
    }
}
