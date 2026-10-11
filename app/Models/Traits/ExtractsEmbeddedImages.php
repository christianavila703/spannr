<?php

namespace App\Models\Traits;

use App\Observers\EmbeddedImageObserver;

/**
 * DEV-15. Put this on a model whose rich text columns are listed in ExtractEmbeddedImages::COLUMNS. When one of those
 * columns is saved with a pasted base64 image in it, the image is moved out into a file (see EmbeddedImageObserver).
 */
trait ExtractsEmbeddedImages
{
    protected static function bootExtractsEmbeddedImages(): void
    {
        static::observe(EmbeddedImageObserver::class);
    }
}
