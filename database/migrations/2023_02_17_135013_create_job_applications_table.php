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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->references('id')->on('people');
            $table->foreignId('job_id')->references('id')->on('jobs');
            $table->foreignId('location_id')->references('id')->on('locations');
            $table->string('status')->default(\App\Models\JobApplication::STATUS_NEW);
            $table->mediumText('message')->nullable();
            $table->integer('recruiter')->default(0);
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
        Schema::dropIfExists('job_applications');
    }
};
