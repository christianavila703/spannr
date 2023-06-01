<?php

namespace App\Http\Livewire\Public\Organizations;

use App\Models\Organization;
use Livewire\Component;
use Livewire\WithPagination;

class OrganizationIndex extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'bootstrap';

    public string $search = '';
    public string $type = '';

    public array $allowedTypes = [
        Organization::TYPE_PUBLIC,
        Organization::TYPE_PRIVATE,
        Organization::TYPE_NONPROFIT,
        Organization::TYPE_EDUCATIONAL,
        'Other'
    ];

    public function mount() {
        $this->type = Organization::TYPE_PUBLIC;
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
            $this->type = Organization::TYPE_PUBLIC;
        }

        $this->resetPage();
    }

    public function setPrivate() {
        $this->type = Organization::TYPE_PRIVATE;
    }

    public function render()
    {
        return view('livewire.public.organizations.organization-index', [
            'typeOptions' => Organization::pluck('type')->unique()->toArray(),
            'organizations' => Organization::with(['people', 'investors', 'fundraisingRounds', 'stock'])
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
                ->orderBy('name')
                ->paginate(48)
        ]);
    }
}
