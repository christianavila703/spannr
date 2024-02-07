<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\Person;
use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'bootstrap';

    public $perPage = 25;

    public $filters = [
        'search' => '',
        'role' => []
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
        $unassignedPeople = Person::doesntHave('user')->orderBy('name')->pluck('name', 'id');

        return view('livewire.admin.users.index', [
            'unassignedPeople' => $unassignedPeople,
            'users' => User::orderByDesc('created_at')->orderBy('name')
                ->with(['roles', 'person'])
                ->when($this->filters['search'], function($query, $search) {
                    return $query
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('title', 'like', '%' . $search . '%')
                        ->orWhere('byline', 'like', '%' . $search . '%')
                        ->orWhereHas('roles', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->when($this->filters['role'], function($query, $value) {
                    return $query->whereHas('roles', function($query) use ($value) {
                        $query->whereIn('name', $value);
                    });
                })
                ->paginate($this->perPage),
            'roleOptions' => Role::pluck('name', 'id')->toArray()
        ]);
    }
}
