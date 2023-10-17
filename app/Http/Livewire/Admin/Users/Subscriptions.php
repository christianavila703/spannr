<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\Person;
use App\Models\User;
use App\Models\UserSubscription;
use Livewire\Component;
use Livewire\WithPagination;

class Subscriptions extends Component
{
    use WithPagination;
    protected string $paginationTheme = 'bootstrap';

    public $search;
    public $perPage = 25;

    public $filters = [
        'search' => '',
    ];


    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function resetFilters() {
        $this->reset('filters');
    }

    public function render()
    {
        return view('livewire.admin.users.subscriptions', [
            'subscriptions' => UserSubscription::with('user')
                ->when($this->filters['search'], function($query, $search) {
                    return $query->whereHas('user', function ($query) use ($search) {
                        return $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    });
                })
                ->latest()
                ->paginate($this->perPage)
        ]);
    }
}
