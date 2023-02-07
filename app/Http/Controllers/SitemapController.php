<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Content;
use App\Models\Glossary;

class SitemapController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sitemap = [];
        $content = Content::published()->get();
        $articles = Article::published()->get();
        $glossary = Glossary::published()->get();

        foreach ($content as $contentItem) {
            $sitemap[] = [
                'url' => $contentItem->link,
                'date' => $contentItem->updated_at->tz('UTC')->toAtomString(),
            ];
        }

        foreach ($articles as $article) {
            $sitemap[] = [
                'url' => $article->url,
                'date' => $article->date->tz('UTC')->toAtomString(),
            ];
        }

        foreach ($glossary as $glossaryItem) {
            $sitemap[] = [
                'url' => $glossaryItem->url,
                'date' => $glossaryItem->updated_at->tz('UTC')->toAtomString(),
            ];
        }

        return response()->view('sitemap', [
            'sitemap' => $sitemap
        ])->header('Content-Type', 'text/xml');
    }
}
