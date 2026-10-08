<?php

namespace Tests\Feature;

use App\Http\Controllers\Public\ContentController;
use Illuminate\Http\Request;
use Illuminate\Routing\RedirectController;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * DEV-2 redirects. Every old URL must answer 301 straight to the final page (one hop, no loop), with or
 * without a query string and a trailing slash, and must never catch the real guides under /articles.
 * No database is used. The 200 checks swap the content controller for a stand-in that only reports which
 * slug the router handed it, so those tests prove the routing, not the page templates.
 */
class SeoRedirectsTest extends TestCase
{
    private const REDIRECTS = [
        '/articles/longevity-supplements-what-they-are-benefits-and-more' => '/articles/best-longevity-supplements',
        '/articles/beat-inflammation-anti-aging-100' => '/articles/chronic-inflammation-and-longevity',
        '/longevity-doctors' => '/articles/longevity-doctors',
        // The two former 302s (Longevity Index pages folded into /index).
        '/index/about' => '/index',
        '/index/terms-and-disclaimers' => '/index',
    ];

    private function useArticleStandIn(): void
    {
        $this->app->bind(ContentController::class, ArticleSlugStandIn::class);
    }

    public static function redirectCases(): array
    {
        $cases = [];

        foreach (self::REDIRECTS as $from => $to) {
            foreach (['plain' => '', 'query string' => '?utm_source=test', 'trailing slash' => '/', 'trailing slash and query' => '/?utm_source=test'] as $label => $suffix) {
                $cases["$from ($label)"] = [$from . $suffix, $to];
            }
        }

        return $cases;
    }

    /**
     * @dataProvider redirectCases
     */
    public function test_old_url_is_a_301_to_the_final_page(string $requested, string $target): void
    {
        $response = $this->get($requested);

        $response->assertStatus(301);
        $location = $response->headers->get('Location');
        $this->assertSame($target, parse_url($location, PHP_URL_PATH), 'wrong target for ' . $requested);
        $this->assertContains(parse_url($location, PHP_URL_HOST), [null, 'localhost'], 'must stay on this site');
    }

    public function test_the_target_does_not_carry_the_tracking_query_and_is_not_a_second_hop(): void
    {
        $response = $this->get('/longevity-doctors?utm_source=test');

        $response->assertStatus(301);
        $this->assertNull(parse_url($response->headers->get('Location'), PHP_URL_QUERY));
    }

    public function test_every_target_is_a_final_page_so_it_is_one_hop_and_never_a_loop(): void
    {
        foreach (self::REDIRECTS as $from => $to) {
            $this->assertArrayNotHasKey($to, self::REDIRECTS, "$from points at $to, which is itself redirected");
            $this->assertNotSame($from, $to);

            $route = Route::getRoutes()->match(Request::create($to));
            $this->assertNotSame(RedirectController::class, ltrim($route->getActionName(), '\\'), "$to is served by a redirect route");
        }
    }

    public function test_the_two_index_pages_use_a_301_not_a_302(): void
    {
        $this->assertSame(301, $this->get('/index/about')->getStatusCode());
        $this->assertSame(301, $this->get('/index/terms-and-disclaimers')->getStatusCode());
    }

    public function test_the_reports_redirect_is_deliberately_still_temporary(): void
    {
        $this->assertSame(302, $this->get('/reports')->getStatusCode());
    }

    public static function guideCases(): array
    {
        return [
            'national guide' => ['/articles/longevity-doctors', 'longevity-doctors'],
            'national guide with query' => ['/articles/longevity-doctors?utm_source=test', 'longevity-doctors'],
            'national guide with trailing slash' => ['/articles/longevity-doctors/', 'longevity-doctors'],
            'city guide' => ['/articles/longevity-doctors-austin-tx', 'longevity-doctors-austin-tx'],
            'city guide with query' => ['/articles/longevity-doctors-austin-tx?utm_source=test', 'longevity-doctors-austin-tx'],
            'city guide with trailing slash' => ['/articles/longevity-doctors-austin-tx/', 'longevity-doctors-austin-tx'],
            'another city guide' => ['/articles/longevity-doctors-new-york-ny', 'longevity-doctors-new-york-ny'],
            'redirect target (supplements)' => ['/articles/best-longevity-supplements', 'best-longevity-supplements'],
            'redirect target (inflammation)' => ['/articles/chronic-inflammation-and-longevity', 'chronic-inflammation-and-longevity'],
            'slug that only starts like an old one' => ['/articles/beat-inflammation-anti-aging-1000', 'beat-inflammation-anti-aging-1000'],
        ];
    }

    /**
     * @dataProvider guideCases
     */
    public function test_real_articles_and_city_guides_are_not_caught_by_a_redirect(string $requested, string $expectedSlug): void
    {
        $this->useArticleStandIn();

        $response = $this->get($requested);

        $response->assertOk();
        $response->assertSee('article-slug:' . $expectedSlug, false);
    }

    public function test_guide_paths_reach_the_article_route_and_the_old_paths_reach_a_redirect_route(): void
    {
        foreach (['/articles/longevity-doctors', '/articles/longevity-doctors-austin-tx'] as $path) {
            $route = Route::getRoutes()->match(Request::create($path));
            $this->assertSame('public.content.articles.show', $route->getName(), $path);
        }

        foreach (array_slice(array_keys(self::REDIRECTS), 0, 3) as $path) {
            $route = Route::getRoutes()->match(Request::create($path));
            $this->assertSame(RedirectController::class, ltrim($route->getActionName(), '\\'), "$path must be a redirect route");
        }
    }
}

/**
 * Stand-in for ContentController in the 200 checks: answers the article route with the slug it received.
 */
class ArticleSlugStandIn
{
    public function articleShow($slug): \Illuminate\Http\Response
    {
        return response('article-slug:' . $slug);
    }
}
