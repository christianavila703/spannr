<?php

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
        try {
            $user = \App\Models\User::create([
                'name' => 'Jack',
                'last_name' => 'Harley',
                'email' => 'writer-jack@spannr.com',
                'byline' => 'Jack Harley, MSc, Oxon',
                'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random())
            ]);

            $user->assignRole('Author');

        } catch(Throwable $exception) {
            \Illuminate\Support\Facades\Log::warning('Error when adding user for Jack ' . $exception->getMessage());
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
