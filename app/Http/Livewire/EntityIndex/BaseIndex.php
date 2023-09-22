<?php

namespace App\Http\Livewire\EntityIndex;

use Livewire\Component;
use Livewire\WithPagination;

class BaseIndex extends Component
{
    use WithPagination;

    const SORT_OPTION_DELIMETER = ':';

    protected $paginationTheme = 'bootstrap';

    public $perPage = 24;
    public $sortColumnName = 'name';
    public $sortDirection = 'asc';
    public $filters = [];

    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function resetFilters() {
        $this->reset('filters');
    }

    public function sortBy($value)
    {
        $sortValues = explode(self::SORT_OPTION_DELIMETER, $value);
        $this->sortColumnName = isset($sortValues[0]) ? $sortValues[0] : 'name';
        $this->sortDirection = isset($sortValues[1])  ? $sortValues[1] : 'asc';
    }

    public function getCurrentSortOption()
    {
        return $this->sortColumnName . self::SORT_OPTION_DELIMETER . $this->sortDirection;
    }
}
