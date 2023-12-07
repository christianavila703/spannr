<?php

namespace App\Http\Livewire\Forms\Marketplace;

use App\Models\FormEntry;
use App\Models\Listing;
use App\Models\MarketplaceLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ListingEmail extends Component
{
    public Listing $listing;
    public $first_name;
    public $last_name;
    public $email;
    public $phone;
    public $message;
    public $ip;

    public ?int $userId = null;

    public $showSuccess = false;
    public $successClass = 'lead text-center text-quaternary';

    public function mount() {
        if (Auth::user()) {
            $this->first_name = Auth::user()->name;
            $this->last_name = Auth::user()->last_name;
            $this->email = Auth::user()->email;
            $this->userId = Auth::id();
        }

        $this->ip = request()->ip();
    }

    protected array $rules = [
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email',
        'phone' => 'nullable',
        'message' => 'required'
    ];

    public function submit() {

        $validated = $this->validate();
        $validated['ip'] = $this->ip;

        $formEntry = FormEntry::create([
            'form' => FormEntry::FORM_MARKETPLACE_LISTING_EMAIL,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'url' => $this->listing->show_url,
            'entity_type' => Listing::class,
            'entity_id' => $this->listing->id,
            'fields' => json_encode($validated),
            'user_id' => $this->userId
        ]);

        MarketplaceLog::create([
            'listing_id' => $this->listing->id,
            'user_id' => $this->userId,
            'action' => MarketplaceLog::TYPE_LISTING_EMAIL,
            'ip' => $this->ip,
            'entity_type' => FormEntry::class,
            'entity_id' => $formEntry->id
        ]);

        $this->showSuccess = true;
    }

    public function render()
    {
        return view('livewire.forms.marketplace.listing-email');
    }
}
