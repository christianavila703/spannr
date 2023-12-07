<?php

namespace App\Http\Livewire\Public\Marketplace\ShowListing;

use App\Models\Listing;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Spatie\SlackAlerts\Facades\SlackAlert;

class MedicalProvider extends Component
{
    protected $listeners = ['loadMap'];

    public Listing $listing;
    public ?string $success = null;

    public function loadMap() {
        $this->dispatchBrowserEvent('load-listing-map');
    }

    public function approveListing() {
        if (Auth::user()->can('edit records')) {
            $this->listing->status = Listing::STATUS_PUBLIC;
            $this->listing->save();
        }
    }

    public function requestApproval() {
        $user = Auth::user();
        SlackAlert::to('marketplace')->message(":incoming_envelope: *Marketplace Approval Request*\n\n*Listing:* <" . $this->listing->show_url . "|{$this->listing->name}>\n*Type:* Clinic\n\n _Approval requested by {$user->email}_");
        $this->success = "Your request has been sent. We'll be in touch soon.";
    }

    public function render()
    {
        return view('livewire.public.marketplace.show-listing.medical-provider');
    }
}
