<?php

namespace App\Http\Livewire\Public\Marketplace;

use App\Http\Livewire\Traits\WithBulkActions;
use App\Http\Livewire\Traits\WithCachedRows;
use App\Http\Livewire\Traits\WithPerPagePagination;
use App\Http\Livewire\Traits\WithSorting;
use App\Models\FormEntry;
use App\Models\Listing;
use App\Models\MarketplaceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Throwable;

class MarketplaceIndex extends Component
{
    use WithPerPagePagination, WithBulkActions, WithCachedRows, WithSorting;

    protected string $paginationTheme = 'bootstrap';
    protected $queryString = ['search', 'lat', 'lon', 'dist'];

    public ?string $search = null;
    public ?string $lat = null;
    public ?string $lon = null;
    public ?string $dist = null;

    public ?string $locationSearch = null;
    public int $locationDistance = 100;

    public array $locationResults = [];

    public array $location = [
        'name' => null,
        'latitude' => null,
        'longitude' => null
    ];

    public array $filters = [
        'treatment' => [],
        'type' => [],
        'age' => [],
        'language' => []
    ];

    public bool $virtual = false;

    public ?string $ip = null;
    public ?int $userId = null;

    public bool $formSuccess = false;
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $email = null;
    public ?string $message = null;
    public ?string $url = null;

    public function mount(Request $request) {
        $this->ip = $request->getClientIp();
        $this->url = route('public.marketplace.index');

        $this->perPage = 20;

        if (Auth::user()) {
            $user = Auth::user();
            $this->userId = $user->id;
            $this->first_name = $user->name;
            $this->last_name = $user->last_name;
            $this->email = $user->email;
        }

        if ($this->lat != null OR $this->lon != null) {

            $this->getLocationFromCoordinates();
            $this->location['latitude'] = $this->lat;
            $this->location['longitude'] = $this->lon;
            $this->locationDistance = $this->dist ?? '100';
        }
    }

    public function getLocationFromCoordinates() {
        $this->location['latitude'] = $this->lat;
        $this->location['longitude'] = $this->lon;

        $url = "https://api.geoapify.com/v1/geocode/reverse?lat={$this->lat}&lon={$this->lon}&apiKey=" . config('services.geoapify.key');

        try {
            $request = Http::get($url);
            $response = json_decode($request->body(), true);

            if (array_key_exists('features', $response)) {
                $this->location['name'] = $response['features'][0]['properties']['city'] . ', ' . $response['features'][0]['properties']['state'] . ', ' . $response['features'][0]['properties']['country'];
            }

        } catch(Throwable $exception) {
            $message = 'Problem during Marketplace getLocationFromCoordinates ' . $exception->getMessage();
            SlackAlert::to('test')->message($message);
            Log::warning($message);
            $this->location['name'] = 'Your Location';
        }
    }

    public function rules() {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ];
    }

    public function submit() {
        $this->validate();

        FormEntry::create([
            'form' => FormEntry::FORM_MARKETPLACE_GENERIC_REQUEST,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'url' => $this->url,
            'user_id' => $this->userId,
            'fields' => json_encode([
                'message' => $this->message,
                'filters' => [
                    $this->filters,
                    'search' => $this->search,
                    'locationSearch' => $this->locationSearch,
                    'locationDistance' => $this->locationDistance,
                    'location' => $this->location,
                    'virtual' => $this->virtual,
                ],
                'ip' => $this->ip,
            ])
        ]);

        $this->formSuccess = true;
    }

    public function gotoPage($page)
    {
        $this->setPage($page);
        $this->emit('gotoTop');
    }

    public function nextPage()
    {
        $this->setPage($this->page + 1);
        $this->emit('gotoTop');
    }

    public function previousPage()
    {
        $this->setPage(max($this->page - 1, 1));
        $this->emit('gotoTop');
    }

    public function updatingSearch() {
        $this->resetPage();
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

        $this->lat = $this->locationResults[$resultId]['properties']['lat'];
        $this->lon = $this->locationResults[$resultId]['properties']['lon'];

        $this->reset('locationSearch');
        $this->reset('locationResults');
    }

    public function updatingFilters() {
        $this->resetPage();
    }

    public function updatingVirtual() {
        $this->resetPage();
    }

    public function clearSearch() {
        $this->reset('search');
        $this->resetPage();
    }

    public function clearVirtual() {
        $this->reset('virtual');
        $this->resetPage();
    }

    public function clearFilters() {
        $this->reset('filters');
        $this->reset('search');
        $this->reset('virtual');
        $this->reset('locationSearch');
        $this->reset('locationResults');
        $this->reset('location');
        $this->reset('locationDistance');
        $this->reset('lat');
        $this->reset('lon');
        $this->resetPage();
    }

    public function clearLocation() {
        $this->reset('location');
    }

    public function clearFilter($filter, $id) {
        unset($this->filters[$filter][$id]);
        $this->resetPage();
    }

    public function go($listingId, $url) {

        $data = [
            'search' => $this->search,
            'locationSearch' => $this->locationSearch,
            'locationDistance' => $this->locationDistance,
            'location' => $this->location,
            'filters' => $this->filters,
            'virtual' => $this->virtual
        ];

        MarketplaceLog::create([
            'listing_id' => $listingId,
            'user_id' => $this->userId,
            'action' => MarketplaceLog::TYPE_LISTING_CLICK_FROM_INDEX,
            'ip' => $this->ip,
            'url' => $url,
            'data' => $data
        ]);

    }

    public function setSearchValue($value) {
        $this->search = $value;
        $this->dispatchBrowserEvent('hide-marketplace-search-presets');
    }

    public function searchMarketplace() {

    }

    public function getRowsQueryProperty()
    {
        $query = Listing::public()
            ->when($this->location['latitude'], function($query) {
                $query->with([
                    'addresses' => function($query) {
                        $query->distance($this->location['latitude'], $this->location['longitude'], $this->locationDistance);
                    },
                    'ageGroups',
                    'languages',
                    'primaryAddress',
                    'treatments'
                ])->whereHas('addresses', function ($query) {
                    $query->distance($this->location['latitude'], $this->location['longitude'], $this->locationDistance);
                });
            }, function($query) {
                $query->with([
                    'addresses',
                    'ageGroups',
                    'languages',
                    'primaryAddress',
                    'treatments'
                ]);
            })
            ->withCount('addresses')
            ->when($this->search, function ($query, $search) {
                return $query
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%')
                    ->orwhereHas('addresses', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })
                    ->orwhereHas('treatments', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->when($this->filters['type'], function($query, $valueArray) {
                return $query->whereIn('type', $valueArray);
            })
            ->when($this->virtual, function($query) {
                return $query->where('virtual', 1);
            })
            ->when($this->filters['treatment'], function($query, $valueArray) {
                return $query->whereHas('treatments', function($query) use ($valueArray) {
                    $query->whereIn('name', $valueArray);
                });
            })
            ->when($this->location['latitude'], function($query) {
                $query->whereHas('addresses', function ($query) {
                    $query->distance($this->location['latitude'], $this->location['longitude'], $this->locationDistance);
                });
            })
            ->when($this->filters['age'], function($query, $valueArray) {
                return $query->whereHas('ageGroups', function($query) use ($valueArray) {
                    $query->whereIn('name', $valueArray);
                });
            })
            ->when($this->filters['language'], function($query, $valueArray) {
                return $query->whereHas('languages', function($query) use ($valueArray) {
                    $query->whereIn('name', $valueArray);
                });
            });

        return $this->applySorting($query);
    }

    public function getRowsProperty()
    {
        return $this->cache(function () {
            return $this->applyPagination($this->rowsQuery);
        });
    }

    public function render()
    {
        return view('livewire.public.marketplace.marketplace-index', [
            'records' => $this->rows,
            'treatmentOptions' => Listing::SOFT_LAUNCH_TREATMENT_OPTIONS,
            'typeOptions' => Listing::pluck('type')->unique()->toArray(),
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
