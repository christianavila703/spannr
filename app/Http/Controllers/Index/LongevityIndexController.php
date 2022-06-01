<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use App\Models\MarketSnapshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class LongevityIndexController extends Controller
{
    public function index() {
        $marketSnapshot = MarketSnapshot::orderByDesc('timestamp')
            ->with(['stockSnapshots'])
            ->first();

        return view('admin.longevity-index.index', [
            'marketSnapshot' => $marketSnapshot
        ]);
    }

    public function show($id) {
        $marketSnapshot = MarketSnapshot::with(['stockSnapshots'])->findOrFail($id);

        return view('admin.longevity-index.index', [
            'marketSnapshot' => $marketSnapshot
        ]);
    }

    public function all() {
        $marketSnapshots = MarketSnapshot::with(['stockSnapshots'])->orderByDesc('created_at')->get();

        return view('admin.longevity-index.all', [
            'marketSnapshots' => $marketSnapshots
        ]);
    }

    public function updateIndex(): \Illuminate\Http\RedirectResponse
    {
        Artisan::call('startMarketSnapshot');
        return redirect()->route('longevity-index')->with('success', 'Getting new market snapshot... standby!');
    }
}
