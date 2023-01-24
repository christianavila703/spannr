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
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('seo_description');
            $table->string('meta_description')->nullable()->after('status');
        });

        Schema::table('glossary', function (Blueprint $table) {
            $table->string('status')->default(\App\Models\Glossary::STATUS_PUBLISHED)->after('group');
            $table->string('meta_description')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('meta_description');
        });

        Schema::table('glossary', function (Blueprint $table) {
            $table->dropColumn('meta_description');
        });
    }
};
