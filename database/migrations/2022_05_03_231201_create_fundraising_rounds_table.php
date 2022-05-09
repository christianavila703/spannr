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
        Schema::create('fundraising_rounds', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('round');
            $table->foreignId('organization_id')->references('id')->on('organizations')->cascadeOnUpdate()->cascadeOnDelete();
            $table->date('date');
            $table->decimal('amount_raised', 16, 0)->nullable();
            $table->string('announcement_url')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('valuation', 16, 0)->nullable();
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
        Schema::dropIfExists('fundraising_rounds');
    }
};
