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

        Permission::create(['name' => 'edit profile']);
        $teamRole = Role::create(['name' => Role::ROLE_SPANNR_TEAM]);
        $teamRole->givePermissionTo('edit profile');

        $contributorRole = Role::create(['name' => Role::ROLE_SPANNR_CONTRIBUTOR]);
        $contributorRole->givePermissionTo('edit profile');

        $team = ['Cody', 'Dillon', 'Sydney', 'Luke', 'Michael', 'Brett', 'Joshua', 'Vsevolod', 'Jael'];

        $teamMembers = User::whereIn('name', $team)->get();

        foreach ($teamMembers as $user) {
            $user->assignRole($teamRole);
            $user->order = array_search($user->name, $team) ?? null;
            $user->save();
        }

        $contributors = User::whereIn('name', ['Jack'])->get();

        foreach ($contributors as $user) {
            $user->assignRole($contributorRole);
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
        Permission::where('name', 'edit profile')->delete();
        Role::where('name', Role::ROLE_SPANNR_TEAM)->delete();
        Role::where('name', Role::ROLE_SPANNR_CONTRIBUTOR)->delete();
    }
};
