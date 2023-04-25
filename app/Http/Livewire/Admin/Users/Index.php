<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\Person;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'bootstrap';

    public $search;
    public $perPage = 25;

    public $filters = [
        'search' => '',
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
                ->with(['roles', 'media', 'person'])
                ->when($this->filters['search'], function($query, $search) {
                    return $query
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhereHas('roles', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->paginate($this->perPage)
        ]);
    }
}
