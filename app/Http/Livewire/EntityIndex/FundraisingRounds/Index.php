<?php

namespace App\Http\Livewire\EntityIndex\FundraisingRounds;

use App\Http\Livewire\EntityIndex\BaseIndex;
use App\Models\FundraisingRound;
use App\Models\Investor;
use App\Models\Organization;
use Carbon\Carbon;

class Index extends BaseIndex
{
    public $sortColumnName = 'date';
    public $sortDirection = 'desc';

    public $filters = [
        'search' => '',
        'date-min' => null,
        'date-max' => null,
        'amount-min' => null,
        'amount-max' => null,
        'rounds' => [],
        'organizations' => [],
        'investors' => [],
    ];

    public $sortOptions = [
        'name:asc' => 'Name A > Z',
        'name:desc' => 'Name Z > A',
        'date:asc' => 'Oldest > Newest',
        'date:desc' => 'Newest > Oldest',
        'amount_raised:asc' => 'Amount Raised Min > Max',
        'amount_raised:desc' => 'Amount Raised Max > Min',
    ];

    public function render()
    {
        $roundOptions = FundraisingRound::pluck('round')->unique()->toArray();
        $organizations = Organization::whereHas('fundraisingRounds')->get();
        $investors = Investor::whereHas('fundraisingRounds')->get();

        $records = FundraisingRound::query()
            ->when($this->filters['search'], function($query, $search) {
                return $query->where(function ($subquery) use ($search) {
                    $subquery->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->when($this->filters['date-min'], fn($query, $date) => $query->where('date', '>=', Carbon::parse($date)))
            ->when($this->filters['date-max'], fn($query, $date) => $query->where('date', '<=', Carbon::parse($date)))
            ->when($this->filters['amount-min'], fn($query, $value) => $query->where('amount_raised', '>=', $value))
            ->when($this->filters['amount-max'], fn($query, $value) => $query->where('amount_raised', '<=', $value))
            ->when($this->filters['rounds'], function($query, $values) {
                $query->whereIn('round', $values);
            })
            ->when($this->filters['organizations'], function($query, $ids) {
                $query->whereIn('organization_id', $ids);
            })
            ->when($this->filters['investors'], function($query, $ids) {
                return $query->whereHas('investors', function ($subquery) use ($ids) {
                    $subquery->whereIn('id', $ids);
                });
            })
            ->orderBy($this->sortColumnName, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.entity-index.fundraising-rounds.index', [
            'currentSortOption' => $this->getCurrentSortOption(),
            'roundOptions' => $roundOptions,
            'organizations' => $organizations,
            'investors' => $investors,
            'records' => $records,
        ]);
    }
}
