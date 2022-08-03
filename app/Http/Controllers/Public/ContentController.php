<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Content;
use App\Models\Glossary;
use App\Models\MarketSnapshot;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ContentController extends Controller
{
    public function __construct() {
       View::share ('showFooter', true);
       View::share ('showHeader', true);
    }

    public function index() {
        $seo = Content::where('slug', 'home')->first();
        return view('public.content.index', [
            'showHeader' => false,
            'seo' => $seo
        ]);
    }

    public function ventures() {
        $seo = Content::where('slug', 'ventures')->first();
        return view('public.content.ventures', [
            'showHeader' => false,
            'showFooter' => false,
            'seo' => $seo
        ]);
    }

    public function terms() {
        $seo = Content::where('slug', 'terms')->first();
        return view('public.content.terms', [
            'seo' => $seo
        ]);
    }

    public function privacy() {
        $seo = Content::where('slug', 'privacy')->first();
        return view('public.content.privacy', [
            'seo' => $seo
        ]);
    }

    public function contact() {
        $seo = Content::where('slug', 'contact')->first();
        return view('public.content.contact', [
            'seo' => $seo
        ]);
    }

    public function emailConfirmed() {
        $seo = Content::where('slug', 'confirmed')->first();
        return view('public.content.email-confirmed', [
            'seo' => $seo
        ]);
    }

    public function articlesIndex() {
        $seo = Content::where('slug', 'articles')->first();
        return view('public.content.articles.index', [
            'seo' => $seo
        ]);
    }

    public function articleShow($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        return view('public.content.articles.show', [
            'article' => $article
        ]);
    }

    public function glossaryIndex(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $seo = Content::where('slug', 'glossary')->first();
        $glossaryItems = Glossary::orderBy('name')->get();
        $alphabet = range('A', 'Z');

        $glossaryArray = array_fill_keys($alphabet, []);

        foreach ($glossaryItems as $glossaryItem) {
            $glossaryArray[$glossaryItem->group][] = [
                'name' => $glossaryItem->name,
                'slug' => $glossaryItem->slug,
                'excerpt' => $glossaryItem->excerpt
            ];
        }

        return view('public.content.glossary.index', [
            'glossary' => $glossaryArray,
            'alphabet' => $alphabet,
            'seo' => $seo
        ]);
    }

    public function glossaryShow($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $glossaryItem = Glossary::where('slug', $slug)->firstOrFail();

        return view('public.content.glossary.show', [
            'glossaryItem' => $glossaryItem
        ]);
    }

    public function longevityStockIndex() {
        $seo = Content::where('slug', 'longevity-stock-index')->first();

        $marketSnapshot = MarketSnapshot::orderByDesc('timestamp')
            ->with(['stockSnapshots'])
            ->first();

        return view('public.content.longevity-stock-index.index', [
            'marketSnapshot' => $marketSnapshot,
            'seo' => $seo
        ]);
    }

    public function longevityStockChart(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
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

    public function organizationShow($slug) {
        $organization = Organization::whereHas('stock')
            ->with(['locations', 'stock', 'stock.latestSnapshot'])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('public.content.organizations.show', [
           'organization' => $organization
        ]);
    }

    public function organizationChart($slug) {
        $organization = Organization::where('slug', $slug)->firstOrFail();
        return view('public.content.organizations.chart', [
            'slug' => $organization->slug
        ]);
    }

    public function organizationChartData($slug) {
        $organization = Organization::where('slug', $slug)->firstOrFail();
        $chartData = json_decode($organization->stock->data, true);

        $chartLabels = [];
        $valuesClose = [];
        $valuesOpen = [];
        $valuesHigh = [];
        $valuesLow = [];

        foreach ($chartData as $data) {
            $chartLabels[] = Carbon::createFromTimestampMsUTC($data['date'])->format('M d, Y');

            $valuesClose[] = [
                'x' => $data['date'],
                'y' => $data['close']
            ];

            $valuesOpen[] = [
                'x' => $data['date'],
                'y' => $data['open']
            ];

            $valuesHigh[] = [
                'x' => $data['date'],
                'y' => $data['high']
            ];

            $valuesLow[] = [
                'x' => $data['date'],
                'y' => $data['low']
            ];
        }

        return json_encode([
            'chartLabels' => $chartLabels,
            'valuesClose' => $valuesClose,
            'valuesOpen' => $valuesOpen,
            'valuesHigh' => $valuesHigh,
            'valuesLow' => $valuesLow
        ], true);
    }
}
