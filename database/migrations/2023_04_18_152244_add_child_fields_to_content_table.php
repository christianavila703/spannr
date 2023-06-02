<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('content', function (Blueprint $table) {
            $table->longText('sources')->nullable()->after('sidebar');
            $table->string('sources_label')->nullable()->after('sources');
            $table->foreignId('parent_id')->nullable()->after('sources_label')->references('id')->on('content')->cascadeOnUpdate()->cascadeOnDelete();
            $table->integer('order')->nullable()->after('parent_id');
        });
    }

    public function down()
    {
        Schema::table('content', function (Blueprint $table) {
            $table->dropColumn('sources');
            $table->dropColumn('sources_label');
            $table->dropColumn('parent_id');
            $table->dropColumn('order');
        });
    }
};
