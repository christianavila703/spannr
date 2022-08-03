<?php

namespace App\Http\Controllers\Admin\Index;

use App\Http\Controllers\Controller;
use App\Models\MarketSnapshot;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

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
        $marketSnapshots = MarketSnapshot::with(['stockSnapshots'])->orderByDesc('created_at')->get();
        $stocks = Stock::with(['snapshots'])
            ->orderBy('status')
            ->orderBy('name')
            ->get();

        return view('admin.longevity-index.all', [
            'marketSnapshots' => $marketSnapshots,
            'stocks' => $stocks
        ]);
    }

    public function updateIndex(): \Illuminate\Http\RedirectResponse
    {
        Artisan::call('snapshot:hourly');
        return redirect()->route('longevity-index')->with('success', 'Getting new market snapshot... standby!');
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
}
