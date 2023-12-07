<?php

namespace App\Http\Livewire\Public\Jobs;

use App\Http\Livewire\Traits\WithCachedRows;
use App\Models\Organization;
use Livewire\Component;
use Livewire\WithPagination;

class OrganizationSelect extends Component
{
    use WithPagination, WithCachedRows;

    protected string $paginationTheme = 'bootstrap';

    public $showSelectOrganization = true;
    public $search;
    public $selectedOrganization;
    public $createNew = false;
    public $perPage = 20;

    public $name;
    public $type;
    public $website;

    protected $rules = [
        'name' => ['required', 'unique:App\Models\Organization,name'],
        'type' => 'required',
        'website' => 'required|url'
    ];

    public function selectOrganization($id) {
        $this->selectedOrganization = Organization::findOrFail($id);
        $this->reset('search');
        $this->resetPage();
    }

    public function updatingSearch() {
        $this->resetPage();
    }

    public function clearOrganization() {
        $this->reset('selectedOrganization');
    }

    public function continueToJob() {
        $this->emit('setOrganization', $this->selectedOrganization->id);
        $this->showSelectOrganization = false;
    }

    public function showForm($value) {
        $this->createNew = $value;
    }

    public function submitOrganization()
    {
        $this->validate();

        $organization = Organization::create([
            'name' => $this->name,
            'website' => $this->website,
            'type' => $this->type
        ]);

        $this->emit('setOrganization', $organization->id);
        $this->showSelectOrganization = false;
    }

    public function getRowsQueryProperty()
    {
        return Organization::orderBy('name')
            ->when($this->search, function($query, $search) {
                return $query
                    ->where('name', 'like', '%' . $search . '%');
            });
    }

    public function getRowsProperty()
    {
        return $this->cache(function () {
            return $this->rowsQuery->paginate($this->perPage);
        });
    }

    public function render()
    {
        return view('livewire.public.jobs.organization-select', [
            'records' => $this->rows,
            'organizationTypes' => Organization::TYPES
        ]);
    }
}
