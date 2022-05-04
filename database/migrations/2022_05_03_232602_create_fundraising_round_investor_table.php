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
        Schema::create('fundraising_round_investor', function (Blueprint $table) {
            $table->foreignId('fundraising_round_id')->references('id')->on('fundraising_rounds')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('investor_id')->references('id')->on('investors')->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('fundraising_round_investor');
    }
};
