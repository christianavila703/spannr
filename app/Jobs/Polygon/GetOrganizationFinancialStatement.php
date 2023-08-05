<?php

namespace App\Jobs\Polygon;

use App\Models\OrganizationFinancialStatement;
use App\Models\Stock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GetOrganizationFinancialStatement implements ShouldQueue
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
        $url = 'https://api.polygon.io/vX/reference/financials?ticker=' . $this->stock->ticker . '&apiKey=' . config('services.polygon.key') . '&order=desc';

        try {
            $request = Http::withHeaders([
                'X-Polygon-Edge-ID' => config('services.polygon.edge-id'),
                'X-Polygon-Edge-IP-Address' => config('services.polygon.edge-ip'),
                'X-Polygon-Edge-User-Agent' => config('services.polygon.edge-user-agent')
            ])->get($url);

            $response = json_decode($request->body(), true);

            if ($response['status'] === "OK") {
                foreach ($response['results'] as $result) {
                    $financialStatement = OrganizationFinancialStatement::where('organization_id', $this->stock->organization_id)
                        ->where('record_id', $result['id'])
                        ->first();

                    //update only TTM records
                    if ($financialStatement && $financialStatement->timeframe == 'ttm') {
                        $financialStatement->fill($result);
                        $financialStatement->save();
                        continue;
                    }

                    if (!$financialStatement) {
                        $financialStatement = new OrganizationFinancialStatement;
                        $financialStatement->organization_id = $this->stock->organization_id;
                        $financialStatement->record_id = $result['id'];
                        $financialStatement->fill($result);
                        $financialStatement->save();
                    }
                }
            }
        } catch (\Throwable $throwable) {
            Log::critical('Error during GetOrganizationFinancialStatement job. Organization ID: ' . $this->stock->organization_id .
                '; Ticker: ' . $this->stock->ticker . '; \n' . $throwable->getMessage());
        }
    }
}
