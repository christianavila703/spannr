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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index();
            $table->string('slug')->nullable()->unique();
            $table->string('type');
            $table->string('status')->default(\App\Models\Listing::STATUS_PENDING);
            $table->string('website')->nullable();
            $table->json('emails')->nullable();
            $table->string('affiliate_link')->nullable();
            $table->string('phone')->nullable();
            $table->nullableMorphs('relatable');
            $table->string('summary')->nullable();
            $table->longText('description')->nullable();
            $table->json('licenses')->nullable();
            $table->json('certifications')->nullable();
            $table->json('payment_options')->nullable();
            $table->boolean('insurance')->nullable();
            $table->boolean('virtual')->nullable();
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
        Schema::dropIfExists('listings');
    }
};
