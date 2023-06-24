<?php

namespace App\Http\Controllers\Public\Content\Investors;

use App\Http\Controllers\Controller;
use App\Models\FundraisingRound;
use App\Models\Investor;
use App\Models\Organization;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class InvestorController extends Controller
{
    public function __construct() {
       View::share ('showFooter', true);
       View::share ('showHeader', true);
    }

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $topInvestorsByDealNumber = Investor::withCount('fundraisingRounds')->orderByDesc('fundraising_rounds_count')->take(10)->get();

        $topInvestorsByCapitalData = Investor::withCount(['fundraisingRounds AS total_raised' => function ($query) {
                    $query->select(DB::raw("SUM(amount_raised) as sum"))->whereNotNull('amount_raised');
                }
            ])->get()->sortByDesc('total_raised')->toArray();

        $topCompaniesByTotalRaisedData = Organization::withCount(['fundraisingRounds AS total_raised' => function ($query) {
                    $query->select(DB::raw("SUM(amount_raised) as sum"))->whereNotNull('amount_raised');
                }
            ])->get()->sortByDesc('total_raised')->toArray();

        $topCompaniesByTotalRaised = array_slice($topCompaniesByTotalRaisedData, 0, 10);

        return view('public.content.investors.index', [
            'topInvestorsByDealNumber' => $topInvestorsByDealNumber,
            'topCompaniesByTotalRaised' => $topCompaniesByTotalRaised,
        ]);
    }
}
