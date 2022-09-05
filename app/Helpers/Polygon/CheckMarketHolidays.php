<?php

namespace App\Helpers\Polygon;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class CheckMarketHolidays {

    public static function checkHolidayStatus($date): bool
    {
        $upcomingHolidays = json_decode(Http::get('https://api.polygon.io/v1/marketstatus/upcoming?apiKey=' . config('services.polygon.key'))->body(), true);
        $holidays = array_column($upcomingHolidays, 'date');
        $today = Carbon::parse($date)->format('Y-m-d');

        if (in_array($today, $holidays)) {
            return true;
        }

        return false;
    }
}
