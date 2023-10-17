<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\FundraisingRound;

class FundraisingRoundController extends Controller
{
    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('panel.fundraising-rounds.index', [
            'showRouteRootPrefix' => 'panel',
        ]);
    }

    public function show($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $fundraisingRound = FundraisingRound::findOrFail($id);

        return view('panel.fundraising-rounds.show', [
            'fundraisingRound' => $fundraisingRound,
            'showRouteRootPrefix' => 'panel',
        ]);
    }
}
