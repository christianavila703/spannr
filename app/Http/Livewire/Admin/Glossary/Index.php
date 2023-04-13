<?php

namespace App\Http\Livewire\Admin\Glossary;

use App\Models\Glossary;
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
        'status' => '',
    ];

    public $sortColumnName = 'name';
    public $sortDirection = 'asc';

    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function resetFilters() {
        $this->reset('filters');
        $this->reset('sortColumnName');
        $this->reset('sortDirection');
        $this->resetPage();
    }

    public function sortBy($columnName)
    {
        if ($this->sortColumnName === $columnName) {
            $this->sortDirection = ($this->sortDirection === 'asc' ? 'desc' : 'asc');
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortColumnName = $columnName;
    }

    public function render()
    {
        return view('livewire.admin.glossary.index', [
            'items' => Glossary::query()
                ->when($this->filters['search'], function($query, $search) {
                    return $query->where(function($query) use ($search) {
                        return $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('excerpt', 'like', '%' . $search . '%')
                            ->orWhere('content', 'like', '%' . $search . '%')
                            ->orWhere('content', 'like', '%' . $search . '%');
                    });
                })
                ->when($this->filters['status'], function($query, $status) {
                    return $query->where('status', $status);
                })
                ->orderBy($this->sortColumnName, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
