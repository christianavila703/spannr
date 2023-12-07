<?php

namespace App\Observers;

use App\Models\ImportResult;

class ImportResultObserver
{
    public function updated(ImportResult $importResult): void
    {
        if ($importResult->type === ImportResult::TYPE_LISTING_IMPORT) {
            $importResult->status = $this->getStatus($importResult);
            $importResult->saveQuietly();
        }
    }

    private function getStatus($model): string
    {
        $errors = [];

        foreach(ImportResult::ERROR_FIELDS as $field) {
            if($model->{$field} !== NULL) {
                $errors[] = $field;
            }
        }

        if (empty($errors)) {
            return ImportResult::STATUS_COMPLETED;
        }

        return ImportResult::STATUS_HAS_ERRORS;
    }
}
