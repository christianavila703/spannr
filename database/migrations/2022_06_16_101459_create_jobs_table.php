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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->string('type');
            $table->foreignId('category_id')->references('id')->on('job_categories');
            $table->string('status');
            $table->morphs('owner');
            $table->longText('description')->nullable();
            $table->date('posted_date');
            $table->date('exp_date')->nullable();
            $table->integer('salary')->nullable();
            $table->decimal('hourly', 8, 2)->nullable();
            $table->string('url')->nullable();
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
        Schema::dropIfExists('jobs');
    }
};
