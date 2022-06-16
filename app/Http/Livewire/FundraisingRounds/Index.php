<?php

namespace App\Http\Livewire\FundraisingRounds;

use App\Models\Activity;
use App\Models\FundraisingRound;
use Carbon\Carbon;
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

    public function resetAmountMax() {
        $this->filters['amount-max'] = null;
    }

    public function resetFilter($filter) {
        $this->filters[$filter] = null;
    }

    public function render()
    {
        return view('livewire.fundraising-rounds.index', [
            'fundraisingRounds' => FundraisingRound::orderByDesc('created_at')
                ->with(['organization', 'investors'])
                ->when($this->filters['date-min'], fn($query, $date) => $query->where('date', '>=', Carbon::parse($date)))
                ->when($this->filters['date-max'], fn($query, $date) => $query->where('date', '<=', Carbon::parse($date)))
                ->when($this->filters['amount-min'], fn($query, $amount) => $query->where('amount_raised', '>=', $amount))
                ->when($this->filters['amount-max'], fn($query, $amount) => $query->where('amount_raised', '<=', $amount))
                ->when($this->filters['search'], function($query, $search) {
                    return $query
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhereHas('investors', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->paginate($this->perPage)
        ]);
    }
}
