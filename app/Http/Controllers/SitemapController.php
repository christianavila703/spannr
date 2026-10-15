<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Content;
use App\Models\Glossary;

class SitemapController extends Controller
{
    /**
     * Only URLs that answer 200, can be indexed and are their own canonical belong in a sitemap.
     * These CMS slugs are published records but their URL redirects elsewhere or is noindex.
     */
    private const NOT_LISTED_CONTENT_SLUGS = [
        'confirmed',                    // noindex (thank-you page after confirming the newsletter)
        'index--about',                 // 301 to /index
        'index--terms-and-disclaimers', // 301 to /index
        'longevity-doctors',            // 301 to /articles/longevity-doctors
        'contact-us',                   // 301 to /contact (DEV-23)
        'about-us',                     // 301 to /about
        'privacy-policy',               // 301 to /privacy
        'terms-of-service',             // 301 to /terms
        'terms-and-conditions',         // 301 to /terms
    ];

    /**
     * Article slugs whose URL now 301-redirects (see the SEO redirects in routes/web.php).
     */
    private const NOT_LISTED_ARTICLE_SLUGS = [
        'longevity-supplements-what-they-are-benefits-and-more',
        'beat-inflammation-anti-aging-100',
    ];

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sitemap = [];
        // Child CMS records (parent_id set) have a link like /page#section, which is not a separate page.
        $content = Content::published()
            ->whereNull('parent_id')
            ->whereNotIn('slug', self::NOT_LISTED_CONTENT_SLUGS)
            ->get();
        $articles = Article::published()
            ->whereNotIn('slug', self::NOT_LISTED_ARTICLE_SLUGS)
            ->get();
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
