<?php

namespace App\Http\Livewire\Users;

use App\Models\Role;
use Livewire\Component;

class RolesList extends Component
{
    public function render()
    {
        return view('livewire.users.roles-list', [
            'roles' => Role::with(['permissions'])->get()
        ]);
    }
}
