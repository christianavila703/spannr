<?php

namespace App\Http\Livewire\LongevityIndex;

use App\Models\MarketSnapshot;
use App\Models\StockSnapshot;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class EditMarketSnapshot extends Component
{
    public MarketSnapshot $marketSnapshot;
    public $success = false;

    protected $rules = [
        'marketSnapshot.market_cap' => 'required|integer',
        'marketSnapshot.index_change' => 'required|numeric',
        'marketSnapshot.index_value' => 'required|numeric',
        'marketSnapshot.cumulative_change' => 'required|numeric',
        'marketSnapshot.notes' => 'nullable|string',
    ];

    public function save()
    {
        $this->validate();
        $this->marketSnapshot->save();
        $this->success = true;
    }

    public function mount(MarketSnapshot $marketSnapshot) {
        $this->marketSnapshot = $marketSnapshot;
    }

    public function render()
    {
        return view('livewire.longevity-index.edit-market-snapshot');
    }
}
