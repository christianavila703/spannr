<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permission = Permission::findOrCreate('view contact info');
        $roles = Role::whereIn('name', [Role::ROLE_ADMIN, Role::ROLE_SUPER_ADMIN])->get();

        foreach ($roles as $role) {
            $role->givePermissionTo($permission);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::where('name', 'view contact info')->delete();
    }
};
