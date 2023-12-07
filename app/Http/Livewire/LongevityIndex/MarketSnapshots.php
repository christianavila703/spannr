<?php

namespace App\Http\Livewire\LongevityIndex;

use App\Http\Livewire\Traits\WithBulkActions;
use App\Http\Livewire\Traits\WithCachedRows;
use App\Http\Livewire\Traits\WithPerPagePagination;
use App\Http\Livewire\Traits\WithSorting;
use App\Models\MarketSnapshot;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class MarketSnapshots extends Component
{
    use WithPerPagePagination, WithBulkActions, WithCachedRows, WithSorting;
    protected $paginationTheme = 'bootstrap';

    public $search;

    public $filters = [
        'search' => '',
        'selected-types' => [MarketSnapshot::TYPE_DAILY],
        'date-min' => null,
        'date-max' => null,
    ];

    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function resetFilters() {
        $this->reset('filters');
    }

    public function resetFilter($filter) {
        $this->filters[$filter] = null;
    }

    public function getRowsQueryProperty()
    {
        $query = MarketSnapshot::orderByDesc('timestamp')
                ->with(['stockSnapshots'])
                ->when($this->filters['date-min'], fn($query, $date) => $query->where('timestamp', '>=', Carbon::parse($date)))
                ->when($this->filters['date-max'], fn($query, $date) => $query->where('timestamp', '<=', Carbon::parse($date)))
                ->when($this->filters['search'], function($query, $search) {
                    return $query
                        ->orWhere('type', 'like', '%' . $search . '%')
                        ->orWhere('timestamp', 'like', '%' . $search . '%')
                        ->orWhere('index_change', 'like', '%' . $search . '%')
                        ->orWhere('index_value', 'like', '%' . $search . '%')
                        ->orWhere('cumulative_change', 'like', '%' . $search . '%')
                        ->orWhere('market_cap', 'like', '%' . $search . '%');
                })
                ->when($this->filters['selected-types'], function($query, $selectedTypes) {
                    return $query->whereIn('type', $selectedTypes);
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
        return view('livewire.longevity-index.market-snapshots', [
            'typeOptions' => MarketSnapshot::pluck('type')->unique()->toArray(),
            'marketSnapshots' => $this->rows,
        ]);
    }
}
