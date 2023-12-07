<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('import_results', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('status');
            $table->nullableMorphs('entity');
            $table->json('data');
            $table->json('exception_errors')->nullable();
            $table->json('tag_errors')->nullable();
            $table->json('address_errors')->nullable();
            $table->json('email_errors')->nullable();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('parent_id')->nullable()->references('id')->on('import_results');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('import_results');
    }
};
