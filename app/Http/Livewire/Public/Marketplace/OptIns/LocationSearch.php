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

class LocationSearch extends Component
{
    // Required Parameters
    public string $url;
    public ?string $search_keyword = null;

    // Other Parameters
    public ?string $zip_code = null;
    public ?string $distance = '100';

    public ?string $ip = null;
    public ?int $userId = null;
    public ?string $currentUrl = null;

    public ?string $error = null;

    public function mount(Request $request) {
        $this->userId = Auth::id() ?? null;
        $this->ip = $request->getClientIp();
        $this->currentUrl = $request->url();
    }

    public function rules() {
        return [
            'zip_code' => 'required|regex:/\b\d{5}\b/',
            'distance' => 'required|int'
        ];
    }

    protected $messages = [
        'zip_code.regex' => 'Please enter a valid US zip code.',
    ];

    public function submit() {
        $this->validate();

        $marketplaceLog = MarketplaceLog::create([
            'user_id' => $this->userId,
            'action' => MarketplaceLog::TYPE_ZIP_CODE_SEARCH,
            'ip' => $this->ip,
            'data' => [
                'zip_code' => $this->zip_code,
                'distance' => $this->distance,
                'current_url' => $this->currentUrl
            ]
        ]);

        $url = "https://api.geoapify.com/v1/geocode/autocomplete?text={$this->zip_code}&apiKey=" . config('services.geoapify.key');

        try {
            $request = Http::get($url);
            $response = json_decode($request->body(), true);

            if (array_key_exists('features', $response)) {
                $locations = $response['features'];

                if (empty($locations)) {
                    $this->error = "We couldn't find a match. Please make sure you entered a valid US zip code.";
                } else {
                    $firstLocation = $locations[0];

                    if ($firstLocation['properties']['result_type'] === 'postcode' AND $firstLocation['properties']['country_code'] === 'us') {
                        $urlParameters = [
                            'lat' => $firstLocation['properties']['lat'],
                            'lon' => $firstLocation['properties']['lon'],
                            'dist' => $this->distance
                        ];

                        if ($this->search_keyword) {
                            $urlParameters['search'] = $this->search_keyword;
                        }

                        $url = route('public.marketplace.index', $urlParameters);

                        $marketplaceLog->url = $url;
                        $marketplaceLog->save();

                        $this->dispatchBrowserEvent('go-to-url', ['url' => $url]);
                    } else {
                        $this->error = "We couldn't find a match. Please make sure you entered a valid US zip code.";
                    }
                }
            }

        } catch(Throwable $exception) {
            $message = 'Problem during Marketplace OptIn LocationSearch geocoding ' . $exception->getMessage();
            SlackAlert::to('test')->message($message);
            Log::warning($message);
            $this->error = "We couldn't find a match. Please try a valid US zip code.";
        }
    }

    public function render()
    {
        return view('livewire.public.marketplace.opt-ins.location-search');
    }
}
