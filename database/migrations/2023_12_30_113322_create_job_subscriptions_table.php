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
        Schema::create('job_subscriptions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignIdFor(\App\Models\Job::class);
            $table->foreignIdFor(\App\Models\User::class);
            $table->string('status')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('last4')->nullable();
            $table->string('brand')->nullable();
            $table->string('exp_year')->nullable();
            $table->string('exp_month')->nullable();
            $table->json('data');
            $table->dateTime('current_period_start')->nullable();
            $table->dateTime('current_period_end')->nullable();
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
        Schema::dropIfExists('job_subscriptions');
    }
};
