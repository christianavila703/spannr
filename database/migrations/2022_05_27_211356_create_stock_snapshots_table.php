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
        Schema::create('stock_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_snapshot_id')->references('id')->on('market_snapshots')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('stock_id')->references('id')->on('stocks')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('current_price', 18, 2)->nullable();
            $table->decimal('yesterday_close', 18, 2)->nullable();
            $table->decimal('market_cap', 18, 0)->nullable();
            $table->decimal('volume', 18, 0)->nullable();
            $table->decimal('today_high', 18, 2)->nullable();
            $table->decimal('today_low', 18, 2)->nullable();
            $table->decimal('previous_day_change_price', 18, 2)->nullable();
            $table->decimal('previous_day_change_percent', 5, 2)->nullable();
            $table->decimal('earnings_per_share', 18, 2)->nullable();
            $table->decimal('price_earning_ratio', 18, 2)->nullable();
            $table->decimal('rolling_year_high_price', 18, 2)->nullable();
            $table->decimal('rolling_year_low_price', 18, 2)->nullable();
            $table->decimal('average_daily_trading_volume', 20, 0)->nullable();
            $table->decimal('outstanding_shares', 20, 0)->nullable();
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
        Schema::dropIfExists('stock_snapshots');
    }
};
