<?php

namespace App\Http\Livewire\Users;

use Livewire\Component;
use Livewire\WithPagination;

class Jobs extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $perPage = 25;

    public $sortColumnName = 'posted_date';
    public $sortDirection = 'desc';

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
        $user = auth()->user();

        return view('livewire.users.jobs', [
            'jobs' => $user->jobs()
                ->orderBy($this->sortColumnName, $this->sortDirection)
                ->paginate($this->perPage)
        ]);
    }
}
