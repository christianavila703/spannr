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
        Schema::create('marketplace_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->nullable()->references('id')->on('listings')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('action');
            $table->string('ip')->nullable();
            $table->string('url')->nullable();
            $table->json('data')->nullable();
            $table->nullableMorphs('entity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marketplace_logs');
    }
};
