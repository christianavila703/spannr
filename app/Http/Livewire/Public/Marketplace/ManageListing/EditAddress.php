<?php

namespace App\Http\Livewire\Public\Marketplace\ManageListing;

use App\Models\Address;
use Livewire\Component;

class EditAddress extends Component
{
    public Address $address;

    protected $rules = [
        'address.street' => 'required',
        'address.street2' => 'nullable',
        'address.city' => 'required',
        'address.state' => 'required',
        'address.state_code' => 'required',
        'address.country' => 'required',
        'address.country_code' => 'required',
        'address.postal_code' => 'required',
        'address.latitude' => 'required',
        'address.longitude' => 'required',
        'address.primary' => 'nullable'
    ];

    public function submit()
    {
        $this->validate();
        $this->address->save();
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Saved address!', 'background' => 'bg-success']);
    }

    public function render()
    {
        return view('livewire.public.marketplace.manage-listing.edit-address');
    }
}
