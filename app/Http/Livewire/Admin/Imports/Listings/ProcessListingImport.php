<?php

namespace App\Http\Livewire\Admin\Imports\Listings;

use App\Jobs\Imports\ProcessImportedListing;
use App\Models\ImportResult;
use Livewire\Component;

class ProcessListingImport extends Component
{
    public ImportResult $importResult;

    public array $tableFields = [
        'name' => 'string',
        'slug' => 'string',
        'type' => 'string',
        'status' => 'string',
        'website' => 'url',
        'emails' => 'array',
        'affiliate_link' => 'url',
        'phone' => 'tel',
        'licenses' => 'array',
        'certifications' => 'array',
        'payment_options' => 'array',
        'insurance' => 'bool',
        'virtual' => 'bool',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public array $errorFields = [
        'Exception' => 'exception_errors',
        'Tag' => 'tag_errors',
        'Address' => 'address_errors',
        'Email' => 'email_errors'
    ];

    public function clearErrors($errorField) {
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Cleared errors - refresh to see changes', 'background' => 'bg-success']);
        $this->importResult->{$errorField} = NULL;
        $this->importResult->save();
        $this->importResult->refresh();
    }

    public function processImport() {
        ProcessImportedListing::dispatch($this->importResult);
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Sending import for processing - refresh to see changes', 'background' => 'bg-success']);
    }

    public function render()
    {
        return view('livewire.admin.imports.listings.process-listing-import');
    }
}
