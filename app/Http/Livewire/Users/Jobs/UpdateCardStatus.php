<?php

namespace App\Http\Livewire\Users\Jobs;

use App\Models\SetupIntent;
use Livewire\Component;

class UpdateCardStatus extends Component
{
    public $setupIntentId;

    public function render()
    {
        return view('livewire.users.jobs.update-card-status', [
            'setupIntent' => SetupIntent::findOrFail($this->setupIntentId)
        ]);
    }
}
