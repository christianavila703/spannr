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
        Schema::create('media_items', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique();
            $table->foreignId('publisher_id')->references('id')->on('media_publishers');
            $table->string('name');
            $table->timestamp('date');
            $table->string('url');
            $table->string('image_url', 500)->nullable();
            $table->mediumText('summary')->nullable();
            $table->json('data');
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
        Schema::dropIfExists('media_items');
    }
};
