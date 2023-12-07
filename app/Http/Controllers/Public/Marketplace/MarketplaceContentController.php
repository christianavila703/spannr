<?php

namespace App\Http\Controllers\Public\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\Schema;

class MarketplaceContentController extends Controller
{
    public function __construct() {
       View::share ('showFooter', true);
       View::share ('showHeader', true);
    }

    public function coldPlunge(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $content = Content::where('slug', 'marketplace--cold-plunge')->first();
        $featuredImage = $content->getFirstMediaUrl('featured');

        $graph = $this->getGraphDetails($content);

        return view('public.marketplace.content.cold-plunge.index', [
            'content' => $content,
            'graph' => $graph,
            'featuredImage' => $featuredImage
        ]);
    }

    public function redLightTherapy(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $content = Content::where('slug', 'marketplace--red-light-therapy')->first();
        $featuredImage = $content->getFirstMediaUrl('featured');

        $graph = $this->getGraphDetails($content);

        return view('public.marketplace.content.red-light-therapy.index', [
            'content' => $content,
            'graph' => $graph,
            'featuredImage' => $featuredImage
        ]);
    }

    public function sauna(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $content = Content::where('slug', 'marketplace--sauna')->first();
        $featuredImage = $content->getFirstMediaUrl('featured');
        $graph = $this->getGraphDetails($content);

        return view('public.marketplace.content.sauna.index', [
            'content' => $content,
            'graph' => $graph,
            'featuredImage' => $featuredImage
        ]);
    }

    private function getGraphDetails(Content $content): Graph
    {
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
                ->setProperty('@id', route('public.marketplace.index'))),
            \Spatie\SchemaOrg\Schema::listItem()
                ->setProperty('position', 3)
                ->item(\Spatie\SchemaOrg\Schema::webPage()
                ->name($content->name)
                ->url($content->url)
                ->setProperty('@id', $content->url))
        ]);

        $graph->article()
            ->headline($content->name)
            ->image($content->shareable_image_url)
            ->mainEntityOfPage($content->url)
            ->description($content->meta_description)
            ->author(Schema::organization()
                ->url(route('home'))
                ->name('Spannr')
            )
            ->datePublished($content->created_at)
            ->dateModified($content->updated_at)
            ->publisher(Schema::organization()
                ->url(route('home'))
                ->name('Spannr')
            );

        return $graph;
    }
}
