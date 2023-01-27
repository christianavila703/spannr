<?php

namespace App\Http\Controllers\Public\Content;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\Schema;

class ReportController extends Controller
{
    public function __construct() {
       View::share ('showFooter', true);
       View::share ('showHeader', true);
    }

    public function longevityReport2022(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $content = Content::where('slug', 'reports--2022-longevity-funding')->first();
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
                ->setProperty('position', 2)
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

        return view('public.content.reports.longevity-report-2022', [
            'content' => $content,
            'graph' => $graph,
            'featuredImage' => $featuredImage
        ]);
    }
}
