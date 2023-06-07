<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tables = [
        'articles',
        'content',
        'events',
        'glossary',
        'investors',
        'jobs',
        'locations',
        'organizations',
        'people',
        'stocks',
        'tags',
        'users',
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $currentTable) {
            Schema::table($currentTable, function(Blueprint $table) {
                $table->fullText('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $currentTable) {
            Schema::table($currentTable, function (Blueprint $table) {
                $table->dropFullText('name');
            });
        }
    }
};
