<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * The sitemap may only list URLs that answer 200, can be indexed and are their own canonical.
 * Tables are created here (in-memory SQLite from phpunit.xml) so no real database is touched.
 */
class SitemapTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        Schema::create('content', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('status')->default('Published');
            $table->string('link')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
        });
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('status')->default('publish');
            $table->dateTime('date')->nullable();
            $table->timestamps();
        });
        Schema::create('glossary', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('status')->default('publish');
            $table->timestamps();
        });

        $now = now()->toDateTimeString();

        $page = fn (string $slug, string $link, string $status = 'Published', ?int $parent = null) => [
            'name' => $slug, 'slug' => $slug, 'link' => $link, 'status' => $status, 'parent_id' => $parent,
            'created_at' => $now, 'updated_at' => $now,
        ];

        DB::table('content')->insert([
            $page('home', url('/')),
            $page('privacy', url('/privacy')),
            $page('marketplace', url('/marketplace')),
            $page('confirmed', url('/confirmed')),
            $page('index--about', url('/index/about')),
            $page('index--terms-and-disclaimers', url('/index/terms-and-disclaimers')),
            $page('longevity-doctors', url('/longevity-doctors')),
            $page('a-draft-page', url('/a-draft-page'), 'Draft'),
        ]);
        // A child section of the privacy page: its link is a #fragment, not a page of its own.
        DB::table('content')->insert($page('cookies', url('/privacy') . '#cookies', 'Published', 2));

        // Articles and glossary use 'publish' / 'draft'; the CMS content table uses 'Published'.
        $article = fn (string $slug, string $status = 'publish') => [
            'name' => $slug, 'slug' => $slug, 'status' => $status, 'date' => $now,
            'created_at' => $now, 'updated_at' => $now,
        ];

        DB::table('articles')->insert([
            $article('best-longevity-supplements'),
            $article('chronic-inflammation-and-longevity'),
            $article('longevity-doctors'),
            $article('longevity-doctors-austin-tx'),
            $article('longevity-supplements-what-they-are-benefits-and-more'),
            $article('beat-inflammation-anti-aging-100'),
            $article('an-unpublished-article', 'draft'),
        ]);

        DB::table('glossary')->insert([
            ['name' => 'NAD', 'slug' => 'nad', 'status' => 'publish', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $this->withoutVite();
    }

    /** @return array<int, string> */
    private function listedUrls(): array
    {
        $response = $this->get('/sitemap.xml');
        $response->assertOk();

        preg_match_all('#<loc>([^<]+)</loc>#', $response->getContent(), $matches);

        return array_map('htmlspecialchars_decode', $matches[1]);
    }

    public function test_redirected_and_noindex_paths_are_not_listed(): void
    {
        $listed = $this->listedUrls();

        foreach ([
            '/confirmed',
            '/index/about',
            '/index/terms-and-disclaimers',
            '/longevity-doctors',
            '/articles/longevity-supplements-what-they-are-benefits-and-more',
            '/articles/beat-inflammation-anti-aging-100',
        ] as $path) {
            $this->assertNotContains(url($path), $listed, "$path must not be in the sitemap");
        }
    }

    public function test_good_pages_are_listed(): void
    {
        $listed = $this->listedUrls();

        foreach ([
            '/',
            '/privacy',
            '/marketplace',
            '/articles/best-longevity-supplements',
            '/articles/chronic-inflammation-and-longevity',
            '/articles/longevity-doctors',
            '/articles/longevity-doctors-austin-tx',
            '/glossary/nad',
        ] as $path) {
            $this->assertContains(url($path), $listed, "$path should be in the sitemap");
        }
    }

    public function test_fragment_links_and_unpublished_records_are_not_listed(): void
    {
        $listed = $this->listedUrls();

        foreach ($listed as $url) {
            $this->assertStringNotContainsString('#', $url, "$url is a fragment, not a page");
        }

        $this->assertNotContains(url('/a-draft-page'), $listed);
        $this->assertNotContains(url('/articles/an-unpublished-article'), $listed);
    }

    public function test_the_list_has_exactly_the_expected_urls(): void
    {
        $this->assertEqualsCanonicalizing([
            url('/'),
            url('/privacy'),
            url('/marketplace'),
            url('/articles/best-longevity-supplements'),
            url('/articles/chronic-inflammation-and-longevity'),
            url('/articles/longevity-doctors'),
            url('/articles/longevity-doctors-austin-tx'),
            url('/glossary/nad'),
        ], $this->listedUrls());
    }
}
