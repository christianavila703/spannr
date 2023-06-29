<?php

namespace App\Http\Livewire\Public\Investors;

use App\Models\Investor;
use App\Models\Organization;
use Livewire\Component;
use Livewire\WithPagination;

class InvestorIndex extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $type = '';

    public string $defaultType = Investor::TYPE_FUND;

    public array $allowedTypes = [
        Investor::TYPE_FUND,
        Investor::TYPE_ANGEL,
        Investor::TYPE_CORPORATE,
        'Other'
    ];

    public function mount() {
        $this->type = $this->defaultType;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
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

    public function setType($type) {
        if (in_array($type, $this->allowedTypes)) {
            $this->type = $type;
        } else {
            $this->type = $this->defaultType;
        }

        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.public.investors.investor-index', [
            'typeOptions' => Investor::pluck('type')->unique()->toArray(),
            'investors' => Investor::with(['organizations'])
                ->withCount(['organizations'])
                ->when($this->search, function($query, $search) {
                    return $query
                        ->where('name', 'like', '%' . $search . '%');
                })
                ->when($this->type, function($query, $type) {
                    if ($type === 'Other') {
                        return $query->whereNotIn('type', $this->allowedTypes);
                    } else {
                        return $query->where('type', $type);
                    }
                })
                ->orderByDesc('organizations_count')
                ->paginate(48)
        ]);
    }
}
