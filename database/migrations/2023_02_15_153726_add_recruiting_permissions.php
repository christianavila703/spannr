<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'recruiting']);
        $recruiter = Role::create(['name' => Role::ROLE_RECRUITER]);
        $recruiter->givePermissionTo('recruiting');

        $users = User::whereIn('name', ['Cody', 'Dillon', 'Luke', 'Sydney', 'Brian'])->get();

        foreach ($users as $user) {
            $user->assignRole($recruiter);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::where('name', 'recruiting')->delete();
        Role::where('name', Role::ROLE_RECRUITER)->delete();
    }
};
