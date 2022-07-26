<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarketSnapshot;
use Illuminate\Http\Request;

class LongevityStockIndexController extends Controller
{
    public function summary() {
        $snapshot = MarketSnapshot::hourly()
            ->orderByDesc('timestamp')
            ->first();

        return [
            'timestamp' => $snapshot->formatted_eastern_timestamp,
            'market_cap' => $snapshot->formatted_market_cap,
            'last' => $snapshot->formatted_index_value,
            'change_dollar' => $snapshot->previous_dollar_change,
            'open' => $snapshot->today_open,
            'high' => $snapshot->today_high,
            'year_low' => $snapshot->year_low,
            'volume' => $snapshot->volume,
            'change_percent' => $snapshot->formatted_index_change,
            'previous_close' => $snapshot->previous_close,
            'low' => $snapshot->today_low,
            'year_high' => $snapshot->year_high,
            'icon' => $snapshot->index_change_icon,
            'color' => $snapshot->index_change_color
        ];
    }

    public function stockList() {
        $snapshot = MarketSnapshot::hourly()
            ->with(['stockSnapshots', 'stockSnapshots.stock'])
            ->orderByDesc('timestamp')
            ->first();

        return $snapshot;
    }
}
