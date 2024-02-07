<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;

class AboutPageOrder extends Component
{
    public $selectedUser;
    public $userOrder;

    public ?string $userSearch = null;
    public array $userResults = [];

    public function selectUser($userId) {
        $this->selectedUser = User::find($userId);
        $this->userOrder = $this->selectedUser->order;
    }

    public function saveOrder() {
        if ($this->userOrder == '') {
            $this->userOrder = null;
        }

        $this->selectedUser->order = $this->userOrder;
        $this->selectedUser->save();
    }

    public function updatedUserSearch() {
        $this->search();
    }

    public function clearSelectedUser() {
        $this->reset('selectedUser');
        $this->reset('userOrder');
    }

    public function clearUserSearch() {
        $this->reset('userSearch');
        $this->reset('userResults');
    }

    public function search() {
        if ($this->userSearch) {
            $this->userResults = User::where('name', 'like', '%' . $this->userSearch . '%')->orWhereHas('roles', function($query) {
                $query->where('name', 'like', '%' . $this->userSearch . '%');
            })->whereNull('order')->get()->toArray();
        } else {
            $this->reset('userSearch');
        }
    }

    public function render()
    {
        return view('livewire.admin.users.about-page-order', [
            'users' => User::with([
                'roles' => function($query) {
                    $query->whereIn('name', [
                        Role::ROLE_SPANNR_TEAM,
                        Role::ROLE_SPANNR_CONTRIBUTOR
                    ]);
                }
            ])->orderBy('order')->whereNotNull('order')->get()
        ]);
    }
}
