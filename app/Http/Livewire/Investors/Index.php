<?php

namespace App\Http\Livewire\Investors;

use App\Models\Investor;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;
    public $perPage = 25;

    public $filters = [
        'search' => '',
        'amount-min' => null,
        'amount-max' => null,
        'selected-types' => []
    ];

    public array $selectedTypes = [];

    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function resetFilters() {
        $this->reset('filters');
    }

    public function render()
    {
        return view('livewire.investors.index', [
            'typeOptions' => Investor::pluck('type')->unique()->toArray(),
            'investors' => Investor::orderByDesc('created_at')
                ->with(['organizations', 'fundraisingRounds'])
                ->when($this->filters['search'], function($query, $search) {
                    return $query
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('summary', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhereHas('organizations', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('fundraisingRounds', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->when($this->filters['selected-types'], function($query, $selectedTypes) {
                    return $query->whereIn('type', $selectedTypes);
                })
                ->paginate($this->perPage)
        ]);
    }
}
