<?php

namespace App\Http\Livewire\LongevityIndex;

use App\Models\StockSnapshot;
use Livewire\Component;

class EditStockSnapshot extends Component
{
    public StockSnapshot $snapshot;

    protected $listeners = ['setStockSnapshot'];

    protected $rules = [
        'snapshot.market_cap' => 'required|integer',
    ];

    public function setStockSnapshot($id) {
        $this->snapshot = StockSnapshot::find($id);
    }

    public function save()
    {
        $this->validate();
        $this->snapshot->save();
    }

    public function render()
    {
        return view('livewire.longevity-index.edit-stock-snapshot');
    }
}
