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
        Schema::create('investor_person', function (Blueprint $table) {
            $table->foreignId('investor_id')->references('id')->on('investors')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('person_id')->references('id')->on('people')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('role')->default('Founder');
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
        Schema::dropIfExists('investor_person');
    }
};
