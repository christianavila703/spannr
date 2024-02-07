<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\Role;
use Livewire\Component;

class RolesList extends Component
{
    public function render()
    {
        return view('livewire.admin.users.roles-list', [
            'roles' => Role::with(['permissions'])->withCount(['users'])->get()
        ]);
    }
}
