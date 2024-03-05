<?php

namespace App\Http\Livewire\Admin\Marketplace;

use App\Models\Listing;
use Livewire\Component;
use Throwable;

class QuickAddListing extends Component
{
    public $importResult;

    public ?string $type = null;

    public ?string $name = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $website = null;
    public ?string $summary = null;

    public string $summaryColor = 'text-body';
    public int $summaryCharacters = 0;

    public ?string $success = null;
    public ?string $error = null;

    public function mount() {
        if ($this->importResult) {
            $this->type = $this->importResult->data['type'] ?? Listing::TYPE_CLINIC;
            $this->name = $this->importResult->data['name'];
            $this->email = $this->importResult->data['emails'] ?? null;
            $this->phone = $this->importResult->data['phone'];
            $this->website = $this->importResult->data['website'];
        }
    }

    public function rules() {
        return [
            'name' => 'required|string|unique:listings',
            'email' => 'nullable|email',
            'website' => 'required|url',
            'phone' => 'nullable|string',
            'summary' => 'nullable|string|max:250',
        ];
    }

    public function submit() {
        $this->validate();

        try {
            $listing = Listing::create([
                'name' => $this->name,
                'website' => $this->website,
                'emails' => [$this->email],
                'phone' => $this->phone,
                'summary' => $this->summary,
                'type' => $this->type,
            ]);

            $this->importResult->entity_type = Listing::class;
            $this->importResult->entity_id = $listing->id;
            $this->importResult->save();

            $this->success = 'Successfully created the listing for ' . $listing->name;
            $this->reset('error');
        } catch(Throwable $exception) {
            $this->error = $exception->getMessage();
        }

//        $this->redirect($listing->show_url);
    }

    public function updatedSummary() {
        $this->summaryCharacters = strlen($this->summary);

        if ($this->summaryCharacters === 0) {
            $this->summaryColor = 'text-body';
        } elseif($this->summaryCharacters <= 250) {
            $this->summaryColor = 'text-success';
        } else {
            $this->summaryColor = 'text-danger';
        }
    }


    public function render()
    {
        return view('livewire.admin.marketplace.quick-add-listing', [
            'typeOptions' => Listing::TYPES
        ]);
    }
}
