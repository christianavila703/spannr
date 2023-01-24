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
        Schema::table('glossary', function (Blueprint $table) {
            $table->longText('seo')->change()->nullable();
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->longText('seo')->change()->nullable();
        });
    }
};
