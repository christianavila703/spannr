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
            $table->string('status')->after('type')->default(\App\Models\MarketSnapshot::STATUS_PENDING);
        });

        \App\Models\MarketSnapshot::where('status', \App\Models\MarketSnapshot::STATUS_PENDING)->update(['status' => \App\Models\MarketSnapshot::STATUS_COMPLETE]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('market_snapshots', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
