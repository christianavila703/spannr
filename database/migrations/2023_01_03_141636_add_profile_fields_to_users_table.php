<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('email');
            $table->string('title')->nullable()->after('slug');
            $table->mediumText('bio')->nullable()->after('title');
            $table->unsignedInteger('order')->nullable()->after('bio');
        });

        $users = User::get();

        foreach ($users as $user) {
            $user->slug = \Illuminate\Support\Str::slug($user->name);
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('title');
            $table->dropColumn('bio');
            $table->dropColumn('order');
        });
    }
};
