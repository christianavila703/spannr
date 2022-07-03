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
        Schema::table('market_snapshots', function (Blueprint $table) {
            $table->dropColumn('daily_index_value');
            $table->dropColumn('daily_index_change');
            $table->decimal('cumulative_change', 16, 4)->nullable()->after('index_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('market_snapshots', function (Blueprint $table) {
            $table->dropColumn('cumulative_index_change');
        });
    }
};
