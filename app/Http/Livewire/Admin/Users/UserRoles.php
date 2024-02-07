<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserRoles extends Component
{
    public User $user;

    public array $options = [];
    public array $colors = [];
    public array $current = [];

    public function mount() {
        $this->getCurrentOptions();
        $roles = Role::with('permissions')->orderBy('name')->get();

        $superAdmin = User::find(Auth::id())->can('admin control');

        if ($superAdmin) {
            $this->options = $roles->pluck('name', 'id')->toArray();
        } else {
            $this->options = $roles->where('name', '!=', Role::ROLE_SUPER_ADMIN)->pluck('name', 'id')->toArray();
        }

        $this->colors = Role::COLORS;
    }

    public function addOption($id, $name) {
        $this->user->roles()->attach($id);
        $this->getCurrentOptions();
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Added role: ' . $name, 'background' => 'bg-success']);
    }

    public function removeOption($id, $name) {
        $this->user->roles()->detach($id);
        $this->getCurrentOptions();
        $this->dispatchBrowserEvent('toast-notification',  ['text' => 'Removed role: ' . $name, 'background' => 'bg-danger']);
    }

    public function getCurrentOptions() {
        $this->user->refresh();
        $this->current = $this->user->roles->pluck('name', 'id')->toArray();
    }

    public function render()
    {
        return view('livewire.admin.users.user-roles');
    }
}
