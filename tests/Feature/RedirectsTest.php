<?php

namespace Tests\Feature;

use App\Http\Controllers\SitemapController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Feature\Concerns\BuildsPublicPageTables;
use Tests\TestCase;

/**
 * DEV-23. Old URLs from the server 404 log now 301 to the page that exists (never the homepage), the DEV-2 redirects and
 * the retired /jobs and /investors redirects are unchanged, and paths that should stay 404 still do.
 *
 * In-memory SQLite: no real database is touched.
 */
class RedirectsTest extends TestCase
{
    use BuildsPublicPageTables;

    /** old path => the page it must 301 to */
    private const ADDED = [
        '/contact-us' => '/contact',
        '/about-us' => '/about',
        '/privacy-policy' => '/privacy',
        '/terms-of-service' => '/terms',
        '/terms-and-conditions' => '/terms',
        '/sitemap_index.xml' => '/sitemap.xml',
        '/sitemap-index.xml' => '/sitemap.xml',
        '/marketplace/clinic' => '/marketplace',
        '/marketplace/doctor' => '/marketplace',
    ];


    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        $this->createTables();
        Schema::table('content', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable();
        });

        $this->withoutVite();
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function addedRedirects(): array
    {
        $cases = [];

        foreach (self::ADDED as $from => $to) {
            foreach (['plain' => '', 'query string' => '?utm_source=test', 'trailing slash' => '/'] as $label => $suffix) {
                $cases[$from . ' (' . $label . ')'] = [$from . $suffix, $to];
            }
        }

        return $cases;
    }

    /* ------------------------------------------------------------------ the added redirects */

    /** @dataProvider addedRedirects */
    public function test_each_added_redirect_is_a_301_to_exactly_the_intended_page(string $from, string $to): void
    {
        $response = $this->get($from);

        $response->assertStatus(301);
        $this->assertSame($to, $response->headers->get('Location'), $from);
        $this->assertNotContains($response->headers->get('Location'), ['/', url('/')], 'never the homepage');
    }

    public function test_every_target_is_a_real_page_and_matches_the_named_route(): void
    {
        $routes = app('router')->getRoutes();

        $this->assertSame('/contact', route('public.content.contact', [], false));
        $this->assertSame('/about', route('public.content.about', [], false));
        $this->assertSame('/privacy', route('public.content.privacy', [], false));
        $this->assertSame('/terms', route('public.content.terms', [], false));
        $this->assertSame('/marketplace', route('public.marketplace.index', [], false));

        foreach (array_unique(array_values(self::ADDED)) as $target) {
            $this->assertNotSame('RedirectController', class_basename($routes->match(Request::create($target))->getActionName()), $target . ' is a page, not another redirect');
        }
        $this->assertSame('SitemapController@index', class_basename($routes->match(Request::create('/sitemap.xml'))->getActionName()));
    }

    public function test_a_redirect_is_one_hop_with_no_loop(): void
    {
        foreach (self::ADDED as $from => $to) {
            $this->assertArrayNotHasKey($to, self::ADDED, $from . ' goes straight to a final page, not to another redirect');
            $this->assertNotSame($from, $to);
        }

        $this->assertSame(200, $this->get('/sitemap.xml')->status(), 'the sitemap target answers 200');
    }

    public function test_the_added_redirects_sit_above_the_catch_all_and_next_to_the_existing_ones(): void
    {
        $source = file_get_contents(base_path('routes/web.php'));
        $catchAll = strpos($source, "Route::get('/{slug}'");
        $articleRoute = strpos($source, "Route::get('/articles/{slug}'");
        $existing = strpos($source, "Route::redirect('/longevity-doctors'");

        $this->assertNotFalse($catchAll);
        foreach (array_keys(self::ADDED) as $from) {
            $position = strpos($source, "Route::redirect('" . $from . "'");
            $this->assertNotFalse($position, $from . ' is defined');
            $this->assertGreaterThan($existing, $position, $from . ' comes after the existing article redirects');
            $this->assertLessThan($articleRoute, $position, $from . ' is above /articles/{slug}');
            $this->assertLessThan($catchAll, $position, $from . ' is above the /{slug} catch-all');
        }
    }

    public function test_the_old_cms_style_slugs_are_kept_out_of_the_sitemap(): void
    {
        foreach (['contact', 'contact-us', 'about-us', 'privacy-policy', 'terms-of-service', 'terms-and-conditions'] as $slug) {
            DB::table('content')->insert([
                'name' => $slug, 'slug' => $slug, 'link' => url('/' . $slug), 'status' => 'Published', 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();

        $this->assertStringContainsString('<loc>' . url('/contact') . '</loc>', $xml, 'a real page is listed');
        foreach (['contact-us', 'about-us', 'privacy-policy', 'terms-of-service', 'terms-and-conditions'] as $slug) {
            $this->assertStringNotContainsString('/' . $slug . '<', $xml, $slug . ' redirects, so it is not listed');
        }
        $this->assertSame(5, count(array_intersect(['contact-us', 'about-us', 'privacy-policy', 'terms-of-service', 'terms-and-conditions'],
            (new \ReflectionClassConstant(SitemapController::class, 'NOT_LISTED_CONTENT_SLUGS'))->getValue())));
    }

    /* ------------------------------------------------------------------ what must not change */

    public function test_the_existing_article_redirects_still_301_to_their_current_targets(): void
    {
        $this->get('/articles/beat-inflammation-anti-aging-100')
            ->assertStatus(301)->assertHeader('Location', '/articles/chronic-inflammation-and-longevity');
        $this->get('/articles/longevity-supplements-what-they-are-benefits-and-more')
            ->assertStatus(301)->assertHeader('Location', '/articles/best-longevity-supplements');
        $this->get('/longevity-doctors')
            ->assertStatus(301)->assertHeader('Location', '/articles/longevity-doctors');
    }

    public function test_the_retired_jobs_and_investors_paths_still_301_to_the_homepage(): void
    {
        foreach (['/jobs', '/investors', '/jobs/anything', '/investors/anything'] as $path) {
            $response = $this->get($path);

            $response->assertStatus(301);
            $this->assertContains($response->headers->get('Location'), ['/', url('/')], $path);
        }
    }

    public function test_paths_that_should_stay_404_still_do(): void
    {
        foreach (['/this-page-should-not-exist-xyz', '/api/jobs', '/wp/', '/careers', '/search-jobs', '/feed/', '/blog/', '/2024/', '/marketplace/clinics', '/marketplace/other'] as $path) {
            $this->get($path)->assertStatus(404);
        }
    }

    public function test_a_listing_profile_url_with_a_slug_is_not_caught_by_the_new_marketplace_redirects(): void
    {
        // /marketplace/clinic/some-slug has three segments, so only the bare two-segment paths are redirected.
        $match = app('router')->getRoutes()->match(Request::create('/marketplace/clinic/some-clinic'));

        $this->assertSame('public.marketplace.show', $match->getName());
    }
}
