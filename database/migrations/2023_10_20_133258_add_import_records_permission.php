<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $permission = Permission::create(['name' => 'import records']);
        $roles = Role::where('name', 'Admin')->orWhere('name', 'Super Admin')->get();

        foreach($roles as $role) {
            $role->givePermissionTo($permission);
        }
    }

    public function down()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::where('name', 'import records')->delete();
    }
};
