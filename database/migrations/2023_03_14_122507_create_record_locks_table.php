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
        Schema::create('record_locks', function (Blueprint $table) {
            $table->id();
            $table->morphs('lockable');
            $table->unique(['lockable_id', 'lockable_type']);
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('record_locks');
    }
};
