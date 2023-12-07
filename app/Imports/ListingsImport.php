<?php

namespace App\Imports;

use App\Jobs\Imports\ProcessImportedListing;
use App\Models\ImportResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ListingsImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $parentImport = ImportResult::create([
            'type' => ImportResult::TYPE_LISTING_BATCH,
            'status' => ImportResult::STATUS_COMPLETED,
            'user_id' => Auth::id(),
            'data' => $collection
        ]);

        foreach($collection as $row) {

            $importResult = ImportResult::create([
                'type' => ImportResult::TYPE_LISTING_IMPORT,
                'status' => ImportResult::STATUS_PENDING,
                'user_id' => Auth::id(),
                'data' => $row,
                'parent_id' => $parentImport->id
            ]);

            ProcessImportedListing::dispatch($importResult, $parentImport);
        }
    }
}
