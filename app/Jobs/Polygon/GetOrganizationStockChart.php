<?php

namespace App\Jobs\Polygon;

use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetOrganizationStockChart implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Stock $stock;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Stock $stock)
    {
        $this->stock = $stock;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $start = Carbon::now()->subYears(1)->format('Y-m-d');
        $end = Carbon::now()->format('Y-m-d');

        $url = 'https://api.polygon.io/v2/aggs/ticker/' . $this->stock->ticker . '/range/1/day/' . $start . '/' . $end . '?adjusted=true&sort=asc&apiKey=' . config('services.polygon.key');
        $request = Http::get($url);

        $response = json_decode($request->body(), true);

        if (array_key_exists('results', $response)) {
            $chartDetails = [];

            foreach ($response['results'] as $result) {
                $chartDetails[] = [
                    'date' => $result['t'],
                    'open' => $result['o'],
                    'close' => $result['c'],
                    'low' => $result['l'],
                    'high' => $result['h'],
                ];
            }

            $this->stock->data = json_encode($chartDetails);
            $this->stock->save();
        }
    }
}
