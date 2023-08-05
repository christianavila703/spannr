<?php

namespace App\Console\Commands;

use App\Jobs\Polygon\GetOrganizationFinancialStatement;
use App\Models\Organization;
use App\Models\OrganizationFinancialStatement;
use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Http;

class PolygonCompanyFinancialStatement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'polygon:company-financial-statement';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull public company financial statement details from Polygon';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $stocks = Stock::active()
            ->whereHas('organization',  function (Builder $query) {
                $query->where('type', Organization::TYPE_PUBLIC);
            })->get();

        foreach ($stocks as $stock) {
            GetOrganizationFinancialStatement::dispatch($stock);
        }

        return 0;
    }
}
