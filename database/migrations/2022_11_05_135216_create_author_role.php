<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
        Permission::create(['name' => 'edit articles']);
        $author = Role::create(['name' => 'Author']);
        $author->givePermissionTo('edit articles');

        $users = User::whereIn('name', ['Cody', 'Dillon', 'Luke', 'Sydney'])->get();

        foreach ($users as $user) {
            $user->assignRole($author);
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
        Permission::where('name', 'edit articles')->delete();
        Role::where('name', 'Author')->delete();
    }
};
