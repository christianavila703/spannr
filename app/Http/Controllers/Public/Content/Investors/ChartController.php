<?php

namespace App\Http\Controllers\Public\Content\Investors;

use App\Http\Controllers\Controller;
use App\Models\FundraisingRound;
use App\Models\Investor;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function investorsByTypePieChartData(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $investorTypes = Investor::pluck('type', 'id')->toArray();
        $investorTypeCounts = array_count_values($investorTypes);

        $data = [];

        $otherCount = 0;

        $namedTypes = [
            Investor::TYPE_FUND,
            Investor::TYPE_ANGEL,
            Investor::TYPE_CORPORATE
        ];

        foreach ($investorTypeCounts as $type => $count) {

            if (in_array($type, $namedTypes)) {
                $data[] = [
                    'category' => $type,
                    'value' => $count
                ];
            } else {
                $otherCount =+ $count;
            }
        }

        $data[] = [
            'category' => 'Other',
            'value' => $otherCount
        ];

        return response(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function totalAmountRaisedLast12Months(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $yearAgo = Carbon::today()->subYear()->startOfMonth()->format('Y-m-d');

        $fundraisingRounds = FundraisingRound::where('date', '>=', $yearAgo)
            ->whereNotNull('amount_raised')
            ->select(DB::raw('sum(amount_raised) as total'), DB::raw("DATE_FORMAT(date,'%Y-%m') as day"))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $data = [];

        foreach ($fundraisingRounds as $round) {
            $date = Carbon::createFromFormat('Y-m', $round->day)->startOfMonth();

            $data[] = [
                'label' => '$' . number_format($round->total, 0) . ' raised in ' . $date->format('M Y'),
                'value' => $round->total,
                'date' => $date->format('Y-m-d')
            ];
        }

        return response(json_encode($data, JSON_PRETTY_PRINT));
    }

    public function amountRaisedPerRoundType(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $fundraisingRounds = FundraisingRound::whereNotNull('amount_raised')
            ->select(DB::raw('sum(amount_raised) as total'), 'round')
            ->groupBy('round')
            ->get();

        $data = [];

        foreach ($fundraisingRounds as $key => $round) {
            $data[$key] = [
                'value' => round($round->total),
                'category' => $round->round,
            ];
        }

        ksort($data);
        $data = array_values($data);

        return response(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function averageRaisedPerRoundType(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $fundraisingRounds = FundraisingRound::whereNotNull('amount_raised')->get()->groupBy('round');

        $data = [];

        foreach ($fundraisingRounds as $key => $round) {
            $data[$key] = [
                'value' => round($round->average('amount_raised')),
                'category' => $key
            ];
        }

        ksort($data);
        $data = array_values($data);

        return response(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function yearToDateAmountRaisedPerCategory(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $tagTotals = [];
        $data = [];

        $organizations = Organization::with(['fundraisingRounds', 'tags'])
            ->whereHas('fundraisingRounds', function(Builder $query) {
                $query->whereNotNull('amount_raised')
                    ->where('date', '>=', Carbon::now()->startOfYear()->format('Y-m-d'));
            })->get();

        foreach ($organizations as $organization) {
            $totalRaised = $organization->fundraisingRounds->sum('amount_raised');

            foreach ($organization->tags as $tag) {
                if (array_key_exists($tag->name, $tagTotals)) {
                    $tagTotals[$tag->name] += $totalRaised;
                } else {
                    $tagTotals[$tag->name] = $totalRaised;
                }
            }
        }

        arsort($tagTotals);

        $topTags = array_slice($tagTotals, 0, 10);

        ksort($topTags);

        foreach ($topTags as $label => $total) {
            $data[] = [
                'category' => $label,
                'value' => $total
            ];
        }

        return response(json_encode($data, JSON_NUMERIC_CHECK));

    }

    public function yearOverYearBarChartRace(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {

        $data = [];
        $startYear = 2014;
        $currentYear = Carbon::now()->format('Y');
        $rounds = array_fill_keys(FundraisingRound::ROUNDS, 0);

        for ($i = $startYear; $i <= $currentYear; $i++){
            $data[$i] = $rounds;
        }

        $fundraisingRoundYearGroups = FundraisingRound::whereNotNull('amount_raised')
            ->where('date', '>=', '2015-01-10')
            ->orderBy('date')
            ->select('*', DB::raw("DATE_FORMAT(date,'%Y') as year"))
            ->get()
            ->groupBy('year');

        foreach ($fundraisingRoundYearGroups as $year => $yearGroup) {
            $roundGroups = $yearGroup->groupBy('round');

            foreach($roundGroups as $round => $roundGroup) {
                $roundTotal = $roundGroup->sum('amount_raised');
                $rounds[$round] += $roundTotal;
                $data[$year] = $rounds;
            }

        }

        $data = json_encode($data, JSON_NUMERIC_CHECK);

        return view('public.content.investors.year-over-year-bar-chart-race', [
            'data' => $data,
            'startYear' => $startYear,
            'currentYear' => $currentYear
        ]);
    }
}
