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
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('image')->nullable()->after('instagram');
        });

        Schema::table('investors', function (Blueprint $table) {
            $table->string('image')->nullable()->after('instagram');
        });

        Schema::table('people', function (Blueprint $table) {
            $table->string('image')->nullable()->after('google_scholar');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('investors', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
