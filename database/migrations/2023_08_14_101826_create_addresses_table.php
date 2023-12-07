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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('street');
            $table->string('street2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('state_code');
            $table->string('country');
            $table->string('country_code');
            $table->string('postal_code')->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->morphs('addressable');
            $table->foreignId('location_id')->nullable()->references('id')->on('locations');
            $table->boolean('primary')->default(0);
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
        Schema::dropIfExists('addresses');
    }
};
