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

        $reviewPermission = Permission::create(['name' => 'review records']);
        $reviewerRole = Role::create(['name' => Role::ROLE_REVIEWER]);
        $reviewerRole->givePermissionTo($reviewPermission);

        $reviewerEmails = ['cody@spannr.com', 'sydney@spannr.com', 'luke@spannr.com', 'brian@spannr.com', 'brett@spannr.com', 'dillon@spannr.com', 'vsevolod@spannr.com'];
        $reviewerUsers = User::whereIn('email', $reviewerEmails)->get();

        foreach ($reviewerUsers as $user) {
            $user->assignRole($reviewerRole);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
