<?php

namespace App\Observers;

use App\Console\Commands\ExtractEmbeddedImages;
use App\Support\EmbeddedImageExtractor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * DEV-15. The TinyMCE editor keeps every inserted or pasted image inside the text as a base64 data URI (it has no upload
 * handler), which made some articles several megabytes of HTML. This runs the same extraction as the
 * content:extract-embedded-images command at save time, so new data URIs never reach the database.
 *
 * It only looks at the columns ExtractEmbeddedImages::COLUMNS lists for the model's table, and only when that column is
 * being changed and its new value contains "data:image/". A conversion problem never reaches the editor: it is logged as a
 * warning and the text is saved as it was typed.
 */
class EmbeddedImageObserver
{
    public function saving(Model $model): void
    {
        foreach (ExtractEmbeddedImages::COLUMNS[$model->getTable()] ?? [] as $column) {
            if (! $model->isDirty($column)) {
                continue;
            }

            $value = $model->getAttribute($column);

            if (! is_string($value) || stripos($value, 'data:image/') === false) {
                continue;
            }

            try {
                $model->setAttribute($column, app(EmbeddedImageExtractor::class)->process($value)['html']);
            } catch (Throwable $exception) {
                Log::warning(sprintf(
                    'Embedded image extraction failed for %s #%s %s, saved unchanged: %s: %s',
                    class_basename($model),
                    $model->getKey() ?? '(new)',
                    $column,
                    get_class($exception),
                    $exception->getMessage(),
                ));
            }
        }
    }
}
