<?php

namespace App\Http\Controllers\Public\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Listing;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\Schema;

class MarketplaceListingsController extends Controller
{
    public function __construct() {
       View::share ('showFooter', true);
       View::share ('showHeader', true);
    }

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $content = Content::where('slug', 'marketplace')->first();
        $featuredImage = $content->getFirstMediaUrl('featured');

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
                ->setProperty('position', 1)
                ->item(\Spatie\SchemaOrg\Schema::webPage()
                ->name('Marketplace')
                ->url(route('public.marketplace.index'))
                ->setProperty('@id', route('public.marketplace.index')))
        ]);

        return view('public.marketplace.index', [
            'content' => $content,
            'graph' => $graph,
            'featuredImage' => $featuredImage
        ]);
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('public.marketplace.create');
    }

    public function show($type, $slug): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $listing = Listing::with(['managers', 'owners'])
            ->where('slug', $slug)
            ->where('type', $type)
            ->firstOrFail();

        $canEditListing = false;
        $canEditUsers = false;

        if (Auth::user()) {
            $managers = $listing->managers->pluck('id')->toArray();
            $owners = $listing->owners->pluck('id')->toArray();

            if (Auth::user()->can('edit records') OR in_array(Auth::id(), $managers) OR in_array(Auth::id(), $owners)) {
                $canEditListing = true;
            }

            if (Auth::user()->can('edit records') OR in_array(Auth::id(), $owners)) {
                $canEditUsers = true;
            }
        }

        if ($listing->status === Listing::STATUS_PENDING AND $canEditListing === false) {
            abort(404);
        }

        return view('public.marketplace.show', [
            'listing' => $listing,
            'canEditListing' => $canEditListing,
            'canEditUsers' => $canEditUsers
        ]);
    }
}
