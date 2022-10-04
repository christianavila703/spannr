<?php

namespace App\Http\Controllers\Admin\Index;

use App\Http\Controllers\Controller;
use App\Jobs\Polygon\CalculateMarketSnapshotData;
use App\Jobs\Polygon\GetEndOfDayMarketSnapshot;
use App\Models\MarketSnapshot;
use App\Models\Organization;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Throwable;

class LongevityIndexController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $marketSnapshot = MarketSnapshot::orderByDesc('timestamp')
            ->with(['stockSnapshots'])
            ->first();

        return view('admin.longevity-index.index', [
            'marketSnapshot' => $marketSnapshot
        ]);
    }

    public function show($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $marketSnapshot = MarketSnapshot::with(['stockSnapshots'])->findOrFail($id);

        return view('admin.longevity-index.index', [
            'marketSnapshot' => $marketSnapshot
        ]);
    }

    public function all(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $stocks = Stock::with(['snapshots'])
            ->orderBy('status')
            ->orderBy('name')
            ->get();

        return view('admin.longevity-index.all', [
            'stocks' => $stocks
        ]);
    }

    public function getHourlySnapshot(): \Illuminate\Http\RedirectResponse
    {
        Artisan::call('snapshot:hourly');
        return redirect()->route('admin.longevity-index.all')->with('success', 'Getting new hourly market snapshot... standby!');
    }

    public function getEndOfDaySnapshot(): \Illuminate\Http\RedirectResponse
    {
        GetEndOfDayMarketSnapshot::dispatch();
        return redirect()->route('admin.longevity-index.all')->with('success', 'Getting new end of day market snapshot... standby!');
    }

    public function chartData(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $now = Carbon::now();
        $marketSnapshots = MarketSnapshot::daily()
            ->orderBy('timestamp')
            ->get();

        $data = [];

        foreach($marketSnapshots as $marketSnapshot) {
            $data[] = [
                'name' => 'test',
                'date' => $marketSnapshot->formatted_date,
                'value' => $marketSnapshot->index_value,
                'formatted_value' => $marketSnapshot->formatted_index_value,
                'formatted_date' => $marketSnapshot->formatted_pretty_date,
                'change' => $marketSnapshot->formatted_index_change
            ];
        }

        $latestHourly = MarketSnapshot::hourly()->orderByDesc('timestamp')->first();

        $data[] = [
            'name' => 'test',
            'date' => $latestHourly->formatted_date,
            'value' => $latestHourly->index_value,
            'formatted_value' => $latestHourly->formatted_index_value,
            'formatted_date' => $latestHourly->formatted_pretty_date,
            'change' => $latestHourly->formatted_index_change
        ];

        return response(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function chart(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('admin.longevity-index.chart');
    }

    public function edit($id) {
        $marketSnapshot = MarketSnapshot::findOrFail($id);

        return view('admin.longevity-index.edit', [
            'marketSnapshot' => $marketSnapshot
        ]);
    }

    public function recalculate($id) {
        $marketSnapshot = MarketSnapshot::findOrFail($id);
        $previousSnapshot = MarketSnapshot::daily()->where('id', '<', $marketSnapshot->id)->orderByDesc('id')->first();
        CalculateMarketSnapshotData::dispatch($marketSnapshot, $previousSnapshot);
        return redirect()->route('admin.longevity-index.all')->with('success', 'Recalculating the index for ' . $marketSnapshot->formatted_date);
    }
}
