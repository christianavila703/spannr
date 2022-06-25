<?php

namespace App\Http\Traits;

use Te7aHoudini\LaravelTrix\Traits\HasTrixRichText;

trait HasRichText {

    use HasTrixRichText;

    public function saveTrixRichText($fieldsData) {
        foreach ($fieldsData as $field => $content) {
            $this->trixRichText()->updateOrCreate([
                'field' => $field,
            ], [
                'field'   => $field,
                'content' => $content,
            ]);
        }
    }

    public function trixRender($field)
    {
        $trixRender = $this->trixRichText->where('field', $field)->first();

        return $trixRender ? $trixRender->content : null;
    }
}
