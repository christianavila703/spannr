<?php

namespace App\Http\Livewire\Public\Marketplace\ShowListing;

use App\Models\Listing;
use App\Models\MarketplaceLog;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WebsiteVisit extends Component
{
    public Listing $listing;
    public ?string $ip = null;
    public ?int $userId = null;

    public function mount() {
        $this->userId = Auth::id() ?? null;
        $this->ip = request()->ip();
    }

    public function go() {

        MarketplaceLog::create([
            'listing_id' => $this->listing->id,
            'user_id' => $this->userId,
            'action' => MarketplaceLog::TYPE_WEBSITE_VISIT,
            'ip' => $this->ip,
            'url' => $this->listing->website
        ]);

        $this->dispatchBrowserEvent('go-to-url', ['url' => $this->listing->website . '?utm_source=spannr&utm_medium=website']);
    }

    public function render()
    {
        return view('livewire.public.marketplace.show-listing.website-visit');
    }
}
