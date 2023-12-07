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
        Schema::create('tag_tag_set', function (Blueprint $table) {
            $table->foreignId('tag_set_id')->references('id')->on('tag_sets')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('tag_id')->references('id')->on('tags')->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('tag_tag_set');
    }
};
