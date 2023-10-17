<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Investor;

class InvestorController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('panel.investors.index', [
            'showRouteRootPrefix' => 'panel',
        ]);
    }

    public function show($slug)
    {
        $investor = Investor::where('slug', $slug)->firstOrFail();

        return view('panel.investors.show', [
            'investor' => $investor,
            'showRouteRootPrefix' => 'panel',
        ]);
    }
}
