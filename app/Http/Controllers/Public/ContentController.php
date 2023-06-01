<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Content;
use App\Models\Glossary;
use App\Models\Role;
use App\Models\User;
use App\Models\MarketSnapshot;
use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Builder;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\Schema;

class ContentController extends Controller
{
    public function __construct() {
       View::share ('showFooter', true);
       View::share ('showHeader', true);
    }

    public function home(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $seo = Content::where('slug', 'home')->first();
        $articles = Article::topInsights()->orderByDesc('date')->with('media')->take(3)->get();
        $organizations = Organization::whereHas('stock', function(Builder $query) {
            $query->indexed()->active();
        })->orderByDesc('updated_at')->take(12)->get();

        return view('public.content.home-redesign', [
            'seo' => $seo,
            'articles' => $articles,
            'organizations' => $organizations
        ]);
    }

    public function ventures(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $seo = Content::where('slug', 'ventures')->first();
        return view('public.content.ventures.index', [
            'seo' => $seo,
            'headerClass' => 'ventures-page'
        ]);
    }

    public function terms(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $seo = Content::where('slug', 'terms')->first();
        return view('public.content.terms', [
            'seo' => $seo
        ]);
    }

    public function privacy(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $seo = Content::where('slug', 'privacy')->first();
        return view('public.content.privacy', [
            'seo' => $seo
        ]);
    }

    public function contact(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $seo = Content::where('slug', 'contact')->first();
        return view('public.content.contact', [
            'seo' => $seo
        ]);
    }

    public function about(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $team = User::orderBy('order')
            ->with(['media', 'person'])
            ->whereHas('roles', function (Builder $query) {
                $query->where('name', Role::ROLE_SPANNR_TEAM);
            })
            ->get();

        $contributors = User::with(['media', 'person'])
            ->whereHas('roles', function (Builder $query) {
                $query->where('name', Role::ROLE_SPANNR_CONTRIBUTOR);
            })
            ->get();

        return view('public.content.about.index', [
            'team' => $team,
            'contributors' => $contributors
        ]);
    }

    public function aboutUser($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $user = User::where('slug', $slug)
            ->whereHas('articles')
            ->with(['articles', 'media', 'person'])
            ->firstOrFail();

        $graph = new Graph();
        $graph->person()
            ->givenName($user->name)
            ->familyName($user->last_name);

        return view('public.content.about.user', [
            'user' => $user,
            'graph' => $graph
        ]);
    }

    public function emailConfirmed(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $seo = Content::where('slug', 'confirmed')->first();
        return view('public.content.email-confirmed', [
            'seo' => $seo
        ]);
    }

    public function articlesIndex(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $seo = Content::where('slug', 'articles')->first();
        $report = Content::where('slug', 'reports--2022-longevity-funding')->first();

        if ($report) {
            $reportImage = $report->getFirstMediaUrl('featured');
        } else {
            $reportImage = NULL;
        }

        return view('public.content.articles.index', [
            'seo' => $seo,
            'report' => $report,
            'reportImage' => $reportImage
        ]);
    }

    public function articleShow($slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        if ($article->status === Article::STATUS_DRAFT) {
            if (!Auth::check()) {
                abort(404);
            }

            $user = Auth::user();
            if (!$user->can('edit articles')) {
                abort(404);
            }
        }

        $pageTitle = $article->page_title ? $article->page_title : $article->name . ' - Spannr';
        $featuredImage = $article->getFirstMediaUrl('featured');

        $graph = new Graph();

        $graph->breadcrumblist()->setProperty('@id', '#breadcrumb');
        $graph->breadcrumbList()->itemListElement([
            \Spatie\SchemaOrg\Schema::listItem()
                ->setProperty('position', 1)
                ->item(\Spatie\SchemaOrg\Schema::webPage()
                ->name('Spannr')
                ->url(route('home'))
                ->setProperty('@id', route('home'))),
            \Spatie\SchemaOrg\Schema::listItem()
                ->setProperty('position', 2)
                ->item(\Spatie\SchemaOrg\Schema::webPage()
                ->name('Insights')
                ->url(route('public.content.articles.index'))
                ->setProperty('@id', route('public.content.articles.index'))),
            \Spatie\SchemaOrg\Schema::listItem()
                ->setProperty('position', 3)
                ->item(\Spatie\SchemaOrg\Schema::webPage()
                ->name($article->name)
                ->url($article->url)
                ->setProperty('@id', $article->url))
        ]);

        $graph->article()
            ->headline($article->name)
            ->image($article->shareable_image_url)
            ->mainEntityOfPage($article->url)
            ->description($article->meta_description)
            ->author(Schema::person()
                ->name($article->author_byline)
                ->url($article->main_author->about_page_url)
                ->jobTitle($article->main_author->title)
            )
            ->datePublished($article->meta_published_time)
            ->dateModified($article->updated_at)
            ->publisher(Schema::organization()
                ->url(route('home'))
                ->name('Spannr')
            );

        return view('public.content.articles.show', [
            'article' => $article,
            'pageTitle' => $pageTitle,
            'featuredImage' => $featuredImage,
            'graph' => $graph
        ]);
    }

    public function glossaryIndex(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $seo = Content::where('slug', 'glossary')->first();
        $glossaryItems = Glossary::published()->orderBy('name')->get();
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

        if ($glossaryItem->status === Glossary::STATUS_DRAFT &&
            (!Auth::check() || !Auth::user()->can('edit articles'))) {
                abort(404);
        }

        $pageTitle = $glossaryItem->page_title ? $glossaryItem->page_title : $glossaryItem->name . ' - Spannr';
        $graph = new Graph();

        $graph->breadcrumblist()->setProperty('@id', '#breadcrumb');
        $graph->breadcrumbList()->itemListElement([
            \Spatie\SchemaOrg\Schema::listItem()
                ->setProperty('position', 1)
                ->item(\Spatie\SchemaOrg\Schema::webPage()
                    ->name('Spannr')
                    ->url(route('home'))
                    ->setProperty('@id', route('home'))),
            \Spatie\SchemaOrg\Schema::listItem()
                ->setProperty('position', 2)
                ->item(\Spatie\SchemaOrg\Schema::webPage()
                    ->name('Glossary')
                    ->url(route('public.content.glossary.index'))
                    ->setProperty('@id', route('public.content.glossary.index'))),
            \Spatie\SchemaOrg\Schema::listItem()
                ->setProperty('position', 3)
                ->item(\Spatie\SchemaOrg\Schema::webPage()
                    ->name($glossaryItem->name)
                    ->url($glossaryItem->url)
                    ->setProperty('@id', $glossaryItem->url))
        ]);

        return view('public.content.glossary.show', [
            'glossaryItem' => $glossaryItem,
            'pageTitle' => $pageTitle,
            'graph' => $graph,
        ]);
    }

    public function longevityStockIndex(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $seo = Content::where('slug', 'index')->first();

        $articles = Article::investing()->orderByDesc('date')->take(6)->get();

        $marketSnapshot = MarketSnapshot::orderByDesc('timestamp')
            ->complete()
            ->hourly()
            ->with(['stockSnapshots'])
            ->first();

        return view('public.content.longevity-stock-index.index', [
            'marketSnapshot' => $marketSnapshot,
            'seo' => $seo,
            'articles' => $articles
        ]);
    }

    public function longevityStockIndexAbout(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $seo = Content::where('slug', 'index--about')->first();
        return view('public.content.wordpress', [
            'seo' => $seo
        ]);
    }

    public function longevityStockIndexTerms(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $seo = Content::where('slug', 'index--terms-and-disclaimers')->first();
        return view('public.content.wordpress', [
            'seo' => $seo
        ]);
    }

    public function longevityStockChart(): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $marketSnapshots = MarketSnapshot::daily()
            ->complete()
            ->orderBy('timestamp')
            ->get();

        $data = [];

        foreach($marketSnapshots as $marketSnapshot) {
            $data[] = [
                'date' => $marketSnapshot->formatted_date,
                'value' => $marketSnapshot->index_value,
                'formatted_value' => $marketSnapshot->formatted_index_value,
                'formatted_date' => $marketSnapshot->formatted_pretty_date,
                'change' => $marketSnapshot->formatted_index_change
            ];
        }

        $lastMarketSnapshot = $marketSnapshots->last();
        $latestHourly = MarketSnapshot::hourly()->complete()->orderByDesc('timestamp')->first();

        if (Carbon::parse($lastMarketSnapshot->timestamp)->format('Y-m-d') !== Carbon::parse($latestHourly->timestamp)->format('Y-m-d')) {
            $latestHourly = MarketSnapshot::hourly()->complete()->orderByDesc('timestamp')->first();
            $data[] = [
                'date' => $latestHourly->formatted_date,
                'value' => $latestHourly->index_value,
                'formatted_value' => $latestHourly->formatted_index_value,
                'formatted_date' => $latestHourly->formatted_pretty_date,
                'change' => $latestHourly->formatted_index_change
            ];
        }

        return response(json_encode($data, JSON_NUMERIC_CHECK));
    }

    public function ventureLogos(Request $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $hideHeader = $request->input('embed') ?? false;

        return view('public.content.ventures.logos', [
            'hideHeader' => $hideHeader
        ]);
    }

    public function leveragingLongevitySanDiego(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $seo = Content::where('slug', 'events--leveraging-longevity-sd')->first();

        return view('public.content.events.leveraging-longevity', [
            'showHeader' => false,
            'seo' => $seo
        ]);
    }

    public function eventsIndex(): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('public.content.events.leveraging-longevity-sd');
    }

    public function eventsConfirmed(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('public.content.events.confirmed', [
            'showHeader' => false,
        ]);
    }

    public function jobPostingSubscriptionConfirmed()
    {
        return view('public.content.jobs.subscription-confirmed', [
            'showHeader' => false,
        ]);
    }
}
