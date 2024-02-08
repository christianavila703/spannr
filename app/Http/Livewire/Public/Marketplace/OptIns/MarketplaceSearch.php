<?php

namespace App\Http\Livewire\Public\Marketplace\OptIns;

use App\Models\MarketplaceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Throwable;

class MarketplaceSearch extends Component
{
    public ?string $ip = null;
    public ?int $userId = null;

    public ?string $search = null;

    public ?string $locationSearch = null;
    public int $locationDistance = 100;

    public array $location = [
        'name' => null,
        'latitude' => null,
        'longitude' => null
    ];

    public array $locationResults = [];

    public bool $spinning = false;

    public function mount(Request $request) {
        $this->ip = $request->getClientIp();

        if (Auth::user()) {
            $user = Auth::user();
            $this->userId = $user->id;
        }
    }

    public function updatingLocationSearch($value) {
        $url = "https://api.geoapify.com/v1/geocode/autocomplete?text={$value}&apiKey=" . config('services.geoapify.key');

        try {
            $request = Http::get($url);
            $response = json_decode($request->body(), true);

            if (array_key_exists('features', $response)) {
                $this->locationResults = $response['features'];
            }

        } catch(Throwable $exception) {
            $message = 'Problem during Marketplace location geocoding ' . $exception->getMessage();
            SlackAlert::to('test')->message($message);
            Log::warning($message);
        }
    }

    public function selectLocation($resultId) {
        $this->location = [
            'name' => $this->locationResults[$resultId]['properties']['formatted'],
            'latitude' => $this->locationResults[$resultId]['properties']['lat'],
            'longitude' => $this->locationResults[$resultId]['properties']['lon'],
        ];

        $this->locationSearch = $this->locationResults[$resultId]['properties']['formatted'];
        $this->reset('locationResults');
    }

    public function setSearchValue($value) {
        $this->search = $value;
        $this->dispatchBrowserEvent('hide-marketplace-search-presets');
    }

    public function submit() {

        $this->spinning = true;

        $data = [
            'search' => $this->search,
            'locationSearch' => $this->locationSearch,
            'locationDistance' => $this->locationDistance,
            'location' => $this->location,
        ];

        $urlAttributes = [];

        if ($this->search) {
            $urlAttributes['search'] = $this->search;
        }

        if ($this->location['latitude'] AND $this->location['longitude']) {
            $urlAttributes['lat'] = $this->location['latitude'];
            $urlAttributes['lon'] = $this->location['longitude'];
            $urlAttributes['dist'] = $this->locationDistance;
        } else {

            if ($this->locationSearch) {

                $url = "https://api.geoapify.com/v1/geocode/autocomplete?text={$this->locationSearch}&apiKey=" . config('services.geoapify.key');

                try {
                    $request = Http::get($url);
                    $response = json_decode($request->body(), true);

                    if (array_key_exists('features', $response)) {
                        $location = $response['features'][0];
                        $urlAttributes['lat'] = $location['properties']['lat'];
                        $urlAttributes['lon'] = $location['properties']['lon'];
                        $urlAttributes['dist'] = $this->locationDistance;
                    }

                } catch(Throwable $exception) {
                    $message = 'Problem during Marketplace location geocoding during submit' . $exception->getMessage();
                    SlackAlert::to('test')->message($message);
                    Log::warning($message);
                }
            }
        }

        MarketplaceLog::create([
            'user_id' => $this->userId,
            'action' => MarketplaceLog::TYPE_MARKETPLACE_SEARCH,
            'ip' => $this->ip,
            'url' => url('/'),
            'data' => $data
        ]);

        if ($urlAttributes) {
            $this->dispatchBrowserEvent('go-to-url', ['url' => route('public.marketplace.index', $urlAttributes)]);
        } else {
            $this->dispatchBrowserEvent('go-to-url', ['url' => route('public.marketplace.index')]);
        }

    }

    public function render()
    {
        return view('livewire.public.marketplace.opt-ins.marketplace-search', [
            'presetOptions' => [
                'cold plunge',
                'red light therapy',
                'sauna',
                'cryotherapy',
                'iv therapy'
            ]
        ]);
    }
}
