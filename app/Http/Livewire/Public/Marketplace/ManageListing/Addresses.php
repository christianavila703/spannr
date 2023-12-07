<?php

namespace App\Http\Livewire\Public\Marketplace\ManageListing;

use App\Models\Address;
use App\Models\Listing;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Throwable;

class Addresses extends Component
{
    public Listing $listing;
    public ?string $locationSearch = null;
    public array $results = [];
    public bool $showResults = false;
    public bool $showForm = false;

    public ?string $locationSearchError = null;

    public $street;
    public $street2;
    public $city;
    public $state;
    public $state_code;
    public $country;
    public $country_code;
    public $postal_code;
    public $latitude;
    public $longitude;

    public function rules() {
        return [
            'street' => 'required',
            'street2' => 'nullable',
            'city' => 'required',
            'state' => 'required',
            'state_code' => 'required',
            'country' => 'required',
            'country_code' => 'required',
            'postal_code' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ];
    }

    public function submit() {
        $this->validate();
        $this->saveLocation();
    }

    public function searchLocation() {

        if ($this->locationSearch === null) {
            $this->locationSearchError = 'Try searching for your address';
            return;
        }

        $url = "https://api.geoapify.com/v1/geocode/autocomplete?text={$this->locationSearch}&street=street&apiKey=" . config('services.geoapify.key');

        try {
            $request = Http::get($url);
            $response = json_decode($request->body(), true);

            if (array_key_exists('features', $response)) {
                $this->results = $response['features'];
            }
            $this->showResults = true;
            $this->showForm = true;
            $this->reset('locationSearchError');
        } catch(Throwable $exception) {
            $message = 'Problem during Marketplace location geocoding ' . $exception->getMessage();
            SlackAlert::to('test')->message($message);
            Log::warning($message);
        }
    }

    private function saveLocation() {
        try {
            $name = $this->getAddressName();

            $attributes = [
                'name' => $name,
                'street' => $this->street,
                'street2' => $this->street2,
                'city' => $this->city,
                'state' => $this->state,
                'state_code' => $this->state_code,
                'country' => $this->country,
                'country_code' => $this->country_code,
                'postal_code' => $this->postal_code,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude
            ];

            $this->listing->addresses()->create($attributes);

            $this->listing->refresh();

            if (!$this->listing->primaryAddress) {
                $address = $this->listing->addresses->first();
                $address->primary = 1;
                $address->save();
                $this->listing->latitude = $address->latitude;
                $this->listing->longitude = $address->longitude;
                $this->listing->save();
            }

            $this->reset('locationSearch');
            $this->reset('results');
            $this->reset('showResults');
            $this->reset('showForm');

            $this->reset('street');
            $this->reset('street2');
            $this->reset('city');
            $this->reset('state');
            $this->reset('state_code');
            $this->reset('country');
            $this->reset('country_code');
            $this->reset('postal_code');
            $this->reset('latitude');
            $this->reset('longitude');

            $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Address saved!', 'background' => 'bg-success']);
        } catch (Throwable $exception) {
            SlackAlert::to('test')->message('<@sydney> *Exception during saveLocation on Marketplace*' . "\n" . $exception->getMessage());
            $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Sorry there was a problem. If it continues, email help@spannr.com', 'background' => 'bg-danger']);
        }
    }

    private function getAddressName(): string
    {
        $name = $this->street;

        if ($this->street2) {
            $name .= " " . $this->street2;
        }

        $name .= ", " . $this->city;
        $name .= ", " . $this->state;
        $name .= ", " . $this->postal_code;
        return $name;
    }

    public function selectLocation($resultId) {

        $result = $this->results[$resultId];

        try {

            $this->street = $result['properties']['address_line1'];
            $this->city = $result['properties']['city'] ?? $result['properties']['suburb'];
            $this->state = $result['properties']['state'];
            $this->state_code = $result['properties']['state_code'];
            $this->country = $result['properties']['country'];
            $this->country_code = $result['properties']['country_code'];
            $this->latitude = $result['properties']['lat'];
            $this->longitude = $result['properties']['lon'];

            if (array_key_exists('postcode', $result['properties'])) {
                $this->postal_code = $this->results[$resultId]['properties']['postcode'];
            }
        } catch (Throwable $exception) {
            SlackAlert::to('test')->message('<@sydney> *Exception during selectLocation on Marketplace*' . "\n" . $exception->getMessage());
            $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Sorry there was a problem. If it continues, email help@spannr.com', 'background' => 'bg-danger']);
        }
    }

    public function setFavorite($addressId) {

        $this->listing->refresh();

        foreach($this->listing->addresses as $address) {

            if ($address->id === $addressId) {
                $address->primary = true;
                $this->listing->latitude = $address->latitude;
                $this->listing->longitude = $address->longitude;
                $this->listing->save();
            } else {
                $address->primary = false;
            }

            $address->save();
        }

        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Saved primary address', 'background' => 'bg-success']);
    }

    public function removeLocation($addressId) {
        $address = Address::find($addressId);
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Deleted ' . $address->name, 'background' => 'bg-danger']);

        $address->delete();

        $this->listing->refresh();

        if (!$this->listing->primaryAddress) {
            $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Add a primary address to show a map on your listing']);
            $this->listing->latitude = NULL;
            $this->listing->longitude = NULL;
            $this->listing->save();
        }
    }

    public function render()
    {
        return view('livewire.public.marketplace.manage-listing.addresses');
    }
}
