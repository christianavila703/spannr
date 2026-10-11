<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Article;
use App\Models\Listing;
use App\Support\StructuredData;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Vite;
use Tests\TestCase;

/**
 * DEV-5. JSON-LD for the homepage, articles, glossary entries, clinic and doctor listings, the breadcrumbs, the hand-set
 * "Last updated" date, and the "All longevity doctors by city" fragment link.
 *
 * Every test decodes the JSON-LD from the rendered HTML, the way a crawler reads it.
 * The tables are created here (in-memory SQLite from phpunit.xml) so no real database is touched.
 */
class StructuredDataTest extends TestCase
{
    private const EVIL = 'Evil </script><script>alert(1)</script> <!-- Name';

    /** How "</script>" is written inside the JSON: the backslash-u escapes for "<" and ">". Built in pieces so no tool decodes it. */
    private const HEX_CLOSE = '\\' . 'u003C/script\\' . 'u003E';

    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        $this->createTables();
        $this->fakeVite();
    }

    private function createTables(): void
    {
        // MySQL compares the type column case-insensitively, so /marketplace/clinic/... finds type "Clinic". Match that.
        DB::statement('create table listings (
            id integer primary key autoincrement, name varchar not null unique, slug varchar unique, type varchar collate nocase not null,
            status varchar not null default "Pending", website varchar, emails text, affiliate_link varchar, phone varchar,
            relatable_type varchar, relatable_id integer, summary varchar, description text, licenses text, certifications text,
            payment_options text, insurance integer, virtual integer, latitude varchar, longitude varchar, created_at datetime, updated_at datetime)');

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('street');
            $table->string('street2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('state_code');
            $table->string('country');
            $table->string('country_code');
            $table->string('postal_code')->nullable();
            $table->string('latitude');
            $table->string('longitude');
            $table->morphs('addressable');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->boolean('primary')->default(0);
            $table->timestamps();
        });
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status');
            $table->mediumText('excerpt')->nullable();
            $table->string('image')->nullable();
            $table->string('page_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->longText('content')->nullable();
            $table->longText('sources')->nullable();
            $table->longText('sidebar')->nullable();
            $table->json('faq')->nullable();
            $table->timestamps();
        });
        $this->reviewerMigration()->up();
        $this->contentUpdatedAtMigration()->up();
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('byline')->nullable();
            $table->string('email')->nullable();
            $table->string('slug')->nullable();
            $table->string('title')->nullable();
            $table->mediumText('bio')->nullable();
            $table->timestamps();
        });
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('instagram')->nullable();
            $table->timestamps();
        });
        Schema::create('article_user', function (Blueprint $table) {
            $table->unsignedBigInteger('article_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
        Schema::create('listing_user', function (Blueprint $table) {
            $table->unsignedBigInteger('listing_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role')->nullable();
            $table->timestamps();
        });
        Schema::create('listing_relationships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id')->nullable();
            $table->morphs('listable');
            $table->string('label')->nullable();
            $table->timestamps();
        });
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->timestamps();
        });
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('depth')->default(0);
            $table->timestamps();
        });
        Schema::create('taggables', function (Blueprint $table) {
            $table->unsignedBigInteger('tag_id');
            $table->morphs('taggable');
            $table->timestamps();
        });
        Schema::create('tag_sets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('tag_tag_set', function (Blueprint $table) {
            $table->unsignedBigInteger('tag_id');
            $table->unsignedBigInteger('tag_set_id');
            $table->timestamps();
        });
        Schema::create('seo', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->longText('description')->nullable();
            $table->string('title')->nullable();
            $table->string('image')->nullable();
            $table->string('author')->nullable();
            $table->string('robots')->nullable();
            $table->string('canonical_url')->nullable();
            $table->timestamps();
        });
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->uuid('uuid')->nullable();
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();
        });
        Schema::create('glossary', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('group')->nullable();
            $table->string('status')->nullable();
            $table->string('page_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->mediumText('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();
        });
        Schema::create('content', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique()->nullable();
            $table->string('link')->nullable();
            $table->string('status')->default('Published');
            $table->longText('seo')->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * The layout asks Vite for the built assets, which do not exist in a test run. This stand-in prints nothing for
     * @vite but resolves Vite::asset() the way a real build does (a URL), so the logo can be asserted.
     */
    private function fakeVite(): void
    {
        $this->app->instance(Vite::class, new class
        {
            public function __invoke()
            {
                return '';
            }

            public function asset($asset, $buildDirectory = null)
            {
                return 'https://spannr.test/build/assets/' . basename($asset);
            }

            public function __call($name, $arguments)
            {
                return '';
            }

            public function __toString()
            {
                return '';
            }
        });
        Facade::clearResolvedInstance(Vite::class);
    }

    private function reviewerMigration(): object
    {
        return require base_path('database/migrations/2026_10_08_120000_add_reviewer_fields_to_articles_table.php');
    }

    private function contentUpdatedAtMigration(): object
    {
        return require base_path('database/migrations/2026_10_09_100000_add_content_updated_at_to_articles_table.php');
    }

    /* ------------------------------------------------------------------ helpers */

    /** @return array<int, array<string, mixed>> every JSON-LD block on the page, decoded */
    private function blocks(string $html): array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);

        $blocks = array_map(fn (string $raw) => json_decode($raw, true, 512, JSON_THROW_ON_ERROR), $matches[1]);

        // Nothing is printed that this reader would miss (another attribute order, a block cut short by a stray tag).
        $this->assertSame(substr_count($html, 'application/ld+json'), count($blocks), 'every JSON-LD script tag was decoded');

        return $blocks;
    }

    /** @return array<int, array<string, mixed>> */
    private function ofType(string $html, string $type): array
    {
        return array_values(array_filter($this->blocks($html), fn (array $block) => ($block['@type'] ?? null) === $type));
    }

    /** The one block of this @type, failing if there are none or several. */
    private function one(string $html, string $type): array
    {
        $found = $this->ofType($html, $type);
        $this->assertCount(1, $found, "exactly one {$type} block");

        return $found[0];
    }

    private function types(string $html): array
    {
        return array_map(fn (array $block) => $block['@type'] ?? '(graph)', $this->blocks($html));
    }

    /** No null, no empty or blank string and no empty array anywhere in the block. */
    private function assertNoEmptyValues(array $data, string $path = ''): void
    {
        foreach ($data as $key => $value) {
            $here = $path . '/' . $key;
            $this->assertNotNull($value, "{$here} is null");

            if (is_string($value)) {
                $this->assertNotSame('', trim($value), "{$here} is an empty string");
            }

            if (is_array($value)) {
                $this->assertNotEmpty($value, "{$here} is an empty array");
                $this->assertNoEmptyValues($value, $here);
            }
        }
    }

    private function assertNoRatings(string $html): void
    {
        $this->assertStringNotContainsStringIgnoringCase('aggregateRating', $html);
        $this->assertStringNotContainsString('"review"', $html);
        $this->assertStringNotContainsString('"Review"', $html);
        $this->assertStringNotContainsString('"ratingValue"', $html);
    }

    /**
     * @param array<string, mixed> $article
     * @param array<string, mixed>|null $author null = an article with no author
     * @param array<string, mixed>|null $person the author's Person record (social links)
     */
    private function makeArticle(array $article = [], ?array $author = ['name' => 'Jordan Rivera', 'slug' => 'jordan-rivera', 'title' => 'Staff Writer'], ?array $person = null): Article
    {
        $stamp = '2026-01-10 09:30:00';

        $id = DB::table('articles')->insertGetId(array_merge([
            'date' => $stamp,
            'name' => 'Sleep and Longevity',
            'slug' => 'sleep-and-longevity',
            'status' => 'publish',
            'excerpt' => 'A short excerpt.',
            'meta_description' => 'What sleep does for lifespan.',
            'content' => '<p>Body text of the article.</p>',
            'created_at' => $stamp,
            'updated_at' => $stamp,
        ], $article));

        if ($author !== null) {
            $userId = DB::table('users')->insertGetId(array_merge(['created_at' => $stamp, 'updated_at' => $stamp], $author));
            DB::table('article_user')->insert(['article_id' => $id, 'user_id' => $userId, 'created_at' => $stamp, 'updated_at' => $stamp]);

            if ($person !== null) {
                DB::table('people')->insert(array_merge(['name' => $author['name'], 'user_id' => $userId, 'created_at' => $stamp, 'updated_at' => $stamp], $person));
            }
        }

        return Article::findOrFail($id);
    }

    private function articlePage(string $slug = 'sleep-and-longevity'): string
    {
        return $this->get('/articles/' . $slug)->assertOk()->getContent();
    }

    private function makeListing(string $name = 'Alpha Longevity Clinic', string $type = Listing::TYPE_CLINIC, array $attributes = [], ?array $address = []): Listing
    {
        $listing = Listing::factory()->create(array_merge(['name' => $name, 'type' => $type, 'status' => Listing::STATUS_PUBLIC], $attributes));

        if ($address !== null) {
            Address::factory()->for($listing, 'addressable')->create(array_merge([
                'name' => '100 Main Street, Miami, FL',
                'street' => '100 Main Street',
            ], $address));
        }

        return $listing->fresh();
    }

    private function listingPage(Listing $listing): string
    {
        return $this->get('/marketplace/' . strtolower($listing->type) . '/' . $listing->slug)->assertOk()->getContent();
    }

    private function tag(string $name, ?string $set, Listing $listing): void
    {
        $tagId = DB::table('tags')->insertGetId(['name' => $name, 'slug' => \Illuminate\Support\Str::slug($name), 'created_at' => now(), 'updated_at' => now()]);

        if ($set !== null) {
            $setId = DB::table('tag_sets')->where('name', $set)->value('id')
                ?? DB::table('tag_sets')->insertGetId(['name' => $set, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('tag_tag_set')->insert(['tag_id' => $tagId, 'tag_set_id' => $setId, 'created_at' => now(), 'updated_at' => now()]);
        }

        DB::table('taggables')->insert(['tag_id' => $tagId, 'taggable_type' => Listing::class, 'taggable_id' => $listing->id, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function connect(Listing $from, Listing $to, string $label = 'Medical Director'): void
    {
        DB::table('listing_relationships')->insert([
            'listing_id' => $from->id, 'listable_type' => Listing::class, 'listable_id' => $to->id,
            'label' => $label, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function makeGlossary(array $attributes = []): int
    {
        // The sidebar shows a random OTHER glossary term, and fails when there is none.
        DB::table('glossary')->insert(['name' => 'Sirtuin', 'slug' => 'sirtuin', 'group' => 'S', 'status' => 'publish', 'excerpt' => 'An enzyme.', 'content' => 'x']);

        return DB::table('glossary')->insertGetId(array_merge([
            'name' => 'NAD', 'slug' => 'nad', 'group' => 'N', 'status' => 'publish', 'meta_description' => 'A coenzyme found in every cell.',
            'excerpt' => 'A coenzyme.', 'content' => '<p>NAD is a coenzyme.</p>', 'created_at' => '2025-06-01 08:00:00', 'updated_at' => '2026-09-09 10:00:00',
        ], $attributes));
    }

    /* ------------------------------------------------------------------ the encoder */

    public function test_clean_removes_null_empty_blank_and_empty_arrays_at_every_depth(): void
    {
        $clean = StructuredData::clean([
            'a' => 'kept', 'b' => '', 'c' => '   ', 'd' => null, 'e' => [], 'f' => ['x' => null, 'y' => ''],
            'g' => ['@type' => 'PostalAddress', 'streetAddress' => ''],
            'h' => ['@type' => 'PostalAddress', 'addressLocality' => 'Miami'],
            'i' => 0, 'j' => 0.0, 'k' => [null, 'one', '', 'two'],
        ]);

        $this->assertSame([
            'a' => 'kept',
            'h' => ['@type' => 'PostalAddress', 'addressLocality' => 'Miami'],
            'i' => 0, 'j' => 0.0,
            'k' => ['one', 'two'],
        ], $clean);
    }

    public function test_encode_keeps_slashes_and_unicode_readable_and_hex_escapes_tags(): void
    {
        $json = StructuredData::encode(['@type' => 'Thing', 'url' => 'https://spannr.com/a/b', 'name' => 'Café <b>&</b> </script>']);

        $this->assertStringContainsString('"url":"https://spannr.com/a/b"', $json, 'slashes are not escaped');
        $this->assertStringContainsString('Café', $json, 'unicode is not escaped');
        $this->assertStringNotContainsString('</script>', $json);
        $this->assertStringNotContainsString('<', $json);
        $this->assertStringContainsString(self::HEX_CLOSE, $json);
        $this->assertSame('Café <b>&</b> </script>', json_decode($json, true)['name'], 'the data round-trips unchanged');
    }

    public function test_a_block_with_no_data_is_not_printed_at_all(): void
    {
        $this->assertSame('', StructuredData::encode(['@context' => 'https://schema.org', '@type' => 'Thing', 'name' => '', 'x' => null]));
        $this->assertSame('', (string) StructuredData::scripts([['@type' => 'Thing', 'name' => null]]));
    }

    public function test_bad_utf8_in_the_data_cannot_break_the_page(): void
    {
        $json = StructuredData::encode(['@type' => 'Thing', 'name' => "Caf\xE9"]);

        $this->assertNotSame('', $json);
        $this->assertIsArray(json_decode($json, true));
    }

    /* ------------------------------------------------------------------ articles */

    public function test_an_article_page_has_exactly_one_article_and_one_breadcrumb_block(): void
    {
        $this->makeArticle();

        $html = $this->articlePage();

        $this->assertSame(['Article', 'BreadcrumbList'], $this->types($html), 'nothing else, and no duplicate of the package blocks');
        $this->assertSame(2, substr_count($html, 'application/ld+json'));
    }

    public function test_the_article_has_its_required_properties(): void
    {
        $this->makeArticle([], ['name' => 'Jordan Rivera', 'slug' => 'jordan-rivera', 'title' => 'Staff Writer', 'byline' => 'Dr. J. Rivera']);

        $article = $this->one($this->articlePage(), 'Article');

        $this->assertSame('https://schema.org', $article['@context']);
        $this->assertSame('Sleep and Longevity', $article['headline']);
        $this->assertSame(['@type' => 'WebPage', '@id' => url('/articles/sleep-and-longevity')], $article['mainEntityOfPage']);
        $this->assertSame('What sleep does for lifespan.', $article['description']);
        $this->assertSame(Carbon::parse('2026-01-10 09:30:00')->toIso8601String(), $article['datePublished']);
        $this->assertSame('Person', $article['author']['@type']);
        $this->assertSame('Dr. J. Rivera', $article['author']['name'], 'the same value as the visible byline');
        $this->assertSame(url('/about/jordan-rivera'), $article['author']['url']);
        $this->assertSame('Staff Writer', $article['author']['jobTitle']);
        $this->assertSame('Organization', $article['publisher']['@type']);
        $this->assertSame('Spannr', $article['publisher']['name']);
        $this->assertSame(url('/'), $article['publisher']['url']);
        $this->assertSame(['@type' => 'ImageObject', 'url' => 'https://spannr.test/build/assets/logo-color.png'], $article['publisher']['logo']);
    }

    public function test_date_modified_is_the_hand_set_date_and_never_updated_at(): void
    {
        $this->makeArticle(['content_updated_at' => '2026-03-05', 'updated_at' => '2026-09-01 12:00:00']);

        $article = $this->one($this->articlePage(), 'Article');

        $this->assertSame('2026-03-05', $article['dateModified']);
        $this->assertStringNotContainsString('2026-09-01', json_encode($article), 'updated_at appears nowhere in the block');
    }

    public function test_date_modified_equals_date_published_when_the_hand_set_date_is_empty(): void
    {
        $this->makeArticle(['content_updated_at' => null, 'updated_at' => '2026-09-01 12:00:00']);

        $article = $this->one($this->articlePage(), 'Article');

        $this->assertSame($article['datePublished'], $article['dateModified']);
        $this->assertSame(Carbon::parse('2026-01-10 09:30:00')->toIso8601String(), $article['dateModified']);
    }

    public function test_reviewed_by_appears_only_when_a_reviewer_is_set(): void
    {
        $this->makeArticle();
        $this->assertArrayNotHasKey('reviewedBy', $this->one($this->articlePage(), 'Article'), 'no reviewer');

        DB::table('articles')->update(['reviewer_name' => 'Sam Patel', 'reviewer_title' => 'Registered Dietitian']);
        $article = $this->one($this->articlePage(), 'Article');
        $this->assertSame(['@type' => 'Person', 'name' => 'Sam Patel', 'jobTitle' => 'Registered Dietitian'], $article['reviewedBy']);

        DB::table('articles')->update(['reviewer_title' => null]);
        $this->assertSame(['@type' => 'Person', 'name' => 'Sam Patel'], $this->one($this->articlePage(), 'Article')['reviewedBy'], 'no job title when none is set');
    }

    public function test_a_reviewer_title_or_review_date_alone_is_not_a_reviewer(): void
    {
        $this->makeArticle(['reviewer_name' => null, 'reviewer_title' => 'Registered Dietitian', 'reviewed_at' => '2026-03-05']);

        $this->assertArrayNotHasKey('reviewedBy', $this->one($this->articlePage(), 'Article'));
    }

    public function test_a_blank_reviewer_name_is_not_a_reviewer(): void
    {
        $this->makeArticle(['reviewer_name' => '   ', 'reviewer_title' => 'Registered Dietitian']);

        $this->assertArrayNotHasKey('reviewedBy', $this->one($this->articlePage(), 'Article'));
    }

    public function test_the_author_carries_same_as_links_only_when_their_profile_has_them(): void
    {
        $this->makeArticle([], ['name' => 'Jordan Rivera', 'slug' => 'jordan-rivera'], ['linkedin' => 'in/jordan-rivera', 'website' => 'jordanrivera.com', 'twitter' => 'jrivera']);

        $author = $this->one($this->articlePage(), 'Article')['author'];

        $this->assertSame([
            'https://jordanrivera.com',
            'https://www.twitter.com/jrivera',
            'https://www.linkedin.com/in/jordan-rivera',
        ], $author['sameAs']);
    }

    public function test_an_author_with_no_profile_links_or_no_slug_has_no_same_as_or_url(): void
    {
        // Rendered through the builder: an author with no slug already breaks the visible "By" line of the page itself
        // (existing behaviour, see ArticleAuthorBoxTest), so the page cannot be requested for this case.
        $article = $this->makeArticle([], ['name' => 'Jordan Rivera', 'slug' => null, 'title' => null], ['linkedin' => null, 'website' => '']);

        $author = StructuredData::article($article)['author'];

        $this->assertSame(['@type' => 'Person', 'name' => 'Jordan Rivera', 'url' => null, 'jobTitle' => null, 'sameAs' => []], $author, 'raw, before empty values are removed');
        $this->assertSame(['@type' => 'Person', 'name' => 'Jordan Rivera'], StructuredData::clean($author));
    }

    public function test_an_article_with_no_author_is_credited_to_spannr_the_organization(): void
    {
        $this->makeArticle([], null);

        $author = $this->one($this->articlePage(), 'Article')['author'];

        $this->assertSame(['@type' => 'Organization', 'name' => 'Spannr', 'url' => url('/')], $author, 'matches the visible "By Spannr"');
    }

    public function test_image_appears_only_when_the_article_has_one(): void
    {
        $this->makeArticle();
        $this->assertArrayNotHasKey('image', $this->one($this->articlePage(), 'Article'), 'the site-wide share placeholder is not an article image');

        DB::table('articles')->update(['image' => 'sleep.jpg']);
        $this->assertSame(url('/') . '/storage/images/articles/sleep.jpg', $this->one($this->articlePage(), 'Article')['image']);
    }

    public function test_missing_or_empty_article_data_leaves_the_property_out(): void
    {
        $this->makeArticle(['excerpt' => null, 'meta_description' => '', 'reviewer_name' => '', 'reviewer_title' => ''], ['name' => 'Jordan Rivera', 'slug' => 'jordan-rivera', 'title' => '']);

        $html = $this->articlePage();
        $article = $this->one($html, 'Article');

        foreach (['description', 'image', 'reviewedBy'] as $key) {
            $this->assertArrayNotHasKey($key, $article, $key);
        }
        $this->assertArrayNotHasKey('jobTitle', $article['author']);
        foreach ($this->blocks($html) as $block) {
            $this->assertNoEmptyValues($block);
        }
    }

    public function test_the_article_breadcrumbs_are_home_insights_and_the_page(): void
    {
        $this->makeArticle();

        $crumbs = $this->one($this->articlePage(), 'BreadcrumbList');

        $this->assertSame([
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Insights', 'item' => url('/articles')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => 'Sleep and Longevity', 'item' => url('/articles/sleep-and-longevity')],
        ], $crumbs['itemListElement']);
    }

    public function test_a_published_faq_is_still_one_faq_page_block_next_to_the_article(): void
    {
        $this->makeArticle(['faq' => json_encode([['question' => 'Is sleep important?', 'answer' => 'Yes.']])]);

        $html = $this->articlePage();

        $types = $this->types($html);
        sort($types);
        $this->assertSame(['Article', 'BreadcrumbList', 'FAQPage'], $types, 'one of each');
        $this->assertSame('Is sleep important?', $this->one($html, 'FAQPage')['mainEntity'][0]['name']);
    }

    public function test_a_script_closing_tag_in_an_article_name_cannot_break_out_of_the_block(): void
    {
        $this->makeArticle(['name' => self::EVIL, 'slug' => 'evil'], ['name' => 'Eve </script><script>alert(2)</script>', 'slug' => 'eve']);

        $html = $this->articlePage('evil');

        $this->assertSame(self::EVIL, $this->one($html, 'Article')['headline']);
        $this->assertStringContainsString(self::HEX_CLOSE, $html);
        $this->assertSame(['Article', 'BreadcrumbList'], $this->types($html));
        $this->assertSame(substr_count($html, '<script type="application/ld+json">'), count($this->blocks($html)));
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringNotContainsString('<script>alert(2)</script>', $html);
    }

    public function test_a_data_uri_image_in_the_body_never_reaches_the_head_or_the_json_ld(): void
    {
        // DEV-3: pasted images used to be stored inside the article HTML. Only the body prints them; meta tags and
        // JSON-LD read the SEO record, the meta description and the excerpt, never the body.
        $uri = 'data:image/png;base64,' . base64_encode(str_repeat('x', 3000));
        $this->makeArticle(['content' => '<p>Start</p><p><img alt="Chart" src="' . $uri . '"></p>', 'excerpt' => null, 'meta_description' => null]);

        $html = $this->articlePage();
        $head = substr($html, 0, strpos($html, '</head>'));

        $this->assertStringContainsString($uri, $html, 'the body itself still prints it until the images are extracted');
        $this->assertStringNotContainsString('data:image', $head);
        foreach ($this->blocks($html) as $block) {
            $this->assertStringNotContainsString('data:image', json_encode($block));
        }
        $this->assertArrayNotHasKey('description', $this->one($html, 'Article'));
        $this->assertArrayNotHasKey('image', $this->one($html, 'Article'));
    }

    public function test_an_article_page_has_no_rating_or_review_markup(): void
    {
        $this->makeArticle(['reviewer_name' => 'Sam Patel', 'reviewer_title' => 'RD', 'reviewed_at' => '2026-03-05']);

        $this->assertNoRatings($this->articlePage());
    }

    public function test_an_article_page_has_no_homepage_organization_block(): void
    {
        $this->makeArticle();

        $this->assertSame([], $this->ofType($this->articlePage(), 'Organization'), 'the Organization is only nested as the publisher here');
    }

    /* ------------------------------------------------------------------ glossary entries */

    public function test_a_glossary_page_has_one_article_and_one_breadcrumb_block(): void
    {
        $this->makeGlossary();

        $html = $this->get('/glossary/nad')->assertOk()->getContent();

        $this->assertSame(['Article', 'BreadcrumbList'], $this->types($html));
        $article = $this->one($html, 'Article');
        $this->assertSame('NAD', $article['headline']);
        $this->assertSame('A coenzyme found in every cell.', $article['description']);
        $this->assertSame(['@type' => 'WebPage', '@id' => url('/glossary/nad')], $article['mainEntityOfPage']);
        $this->assertSame('Spannr', $article['publisher']['name']);
        $this->assertNoRatings($html);
    }

    public function test_a_glossary_entry_has_no_hand_set_date_so_date_modified_equals_date_published(): void
    {
        $this->makeGlossary(['created_at' => '2025-06-01 08:00:00', 'updated_at' => '2026-09-09 10:00:00']);

        $article = $this->one($this->get('/glossary/nad')->assertOk()->getContent(), 'Article');

        $expected = Carbon::parse('2025-06-01 08:00:00')->toIso8601String();
        $this->assertSame($expected, $article['datePublished']);
        $this->assertSame($expected, $article['dateModified']);
    }

    public function test_a_glossary_entry_leaves_out_what_it_does_not_have(): void
    {
        $this->makeGlossary(['meta_description' => null]);

        $html = $this->get('/glossary/nad')->assertOk()->getContent();
        $article = $this->one($html, 'Article');

        foreach (['author', 'image', 'description', 'reviewedBy'] as $key) {
            $this->assertArrayNotHasKey($key, $article, $key);
        }
        foreach ($this->blocks($html) as $block) {
            $this->assertNoEmptyValues($block);
        }
    }

    public function test_the_glossary_breadcrumbs_are_home_glossary_and_the_entry(): void
    {
        $this->makeGlossary();

        $crumbs = $this->one($this->get('/glossary/nad')->assertOk()->getContent(), 'BreadcrumbList');

        $this->assertSame([
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Glossary', 'item' => url('/glossary')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => 'NAD', 'item' => url('/glossary/nad')],
        ], $crumbs['itemListElement']);
    }

    public function test_a_script_closing_tag_in_a_glossary_name_is_safely_escaped(): void
    {
        $this->makeGlossary(['name' => self::EVIL, 'slug' => 'evil']);

        $html = $this->get('/glossary/evil')->assertOk()->getContent();

        $this->assertSame(self::EVIL, $this->one($html, 'Article')['headline']);
        $this->assertSame(['Article', 'BreadcrumbList'], $this->types($html));
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    /* ------------------------------------------------------------------ clinic listings */

    public function test_a_clinic_page_has_one_medical_clinic_and_one_breadcrumb_block(): void
    {
        $clinic = $this->makeListing();

        $html = $this->listingPage($clinic);

        $this->assertSame(['MedicalClinic', 'BreadcrumbList'], $this->types($html));
        $this->assertNoRatings($html);
    }

    public function test_a_full_clinic_has_every_requested_property(): void
    {
        $clinic = $this->makeListing('Alpha Longevity Clinic', Listing::TYPE_CLINIC, ['phone' => '305-555-0100', 'website' => 'alphalongevity.com'], [
            'street2' => 'Suite 4', 'postal_code' => '33127', 'latitude' => '25.7617', 'longitude' => '-80.1918',
        ]);
        DB::table('media')->insert([
            'model_type' => Listing::class, 'model_id' => $clinic->id, 'uuid' => 'b8c1c3ee-0000-4000-8000-000000000001', 'collection_name' => 'featured',
            'name' => 'photo', 'file_name' => 'photo.jpg', 'mime_type' => 'image/jpeg', 'disk' => 'public', 'conversions_disk' => 'public', 'size' => 10,
            'manipulations' => '[]', 'custom_properties' => '[]', 'generated_conversions' => '[]', 'responsive_images' => '[]', 'order_column' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->tag('Cryotherapy', 'Medical Provider Treatments', $clinic);
        $this->tag('IV Therapy', 'Medical Provider Treatments', $clinic);
        $this->tag('Healthspan', 'Clinic Focus', $clinic);
        $this->tag('Unrelated tag', null, $clinic);

        $block = $this->one($this->listingPage($clinic), 'MedicalClinic');

        $this->assertSame('Alpha Longevity Clinic', $block['name']);
        $this->assertSame(url('/marketplace/clinic/alpha-longevity-clinic'), $block['url']);
        $this->assertStringContainsString('photo.jpg', $block['image']);
        $this->assertSame($clinic->fresh()->getFirstMediaUrl('featured'), $block['image']);
        $this->assertSame('305-555-0100', $block['telephone']);
        $this->assertSame([
            '@type' => 'PostalAddress', 'streetAddress' => '100 Main Street Suite 4', 'addressLocality' => 'Miami',
            'addressRegion' => 'FL', 'postalCode' => '33127', 'addressCountry' => 'US',
        ], $block['address']);
        $this->assertSame(['@type' => 'GeoCoordinates', 'latitude' => 25.7617, 'longitude' => -80.1918], $block['geo']);
        $this->assertSame('Healthspan', $block['medicalSpecialty']);
        $this->assertSame([
            ['@type' => 'MedicalProcedure', 'name' => 'Cryotherapy'],
            ['@type' => 'MedicalProcedure', 'name' => 'IV Therapy'],
        ], $block['availableService']);
        $this->assertSame(['https://alphalongevity.com'], $block['sameAs']);
    }

    public function test_a_clinic_with_only_a_name_has_no_other_property(): void
    {
        $clinic = $this->makeListing('Bare Clinic', Listing::TYPE_CLINIC, ['summary' => null], null);

        $html = $this->listingPage($clinic);
        $block = $this->one($html, 'MedicalClinic');

        $this->assertSame(['@context', '@type', 'name', 'url'], array_keys($block));
        foreach ($this->blocks($html) as $each) {
            $this->assertNoEmptyValues($each);
        }
    }

    public function test_blank_clinic_fields_leave_their_properties_out(): void
    {
        $clinic = $this->makeListing('Blank Clinic', Listing::TYPE_CLINIC, ['phone' => '  ', 'website' => ''], ['street2' => '', 'postal_code' => '', 'latitude' => 'n/a', 'longitude' => '']);

        $block = $this->one($this->listingPage($clinic), 'MedicalClinic');

        foreach (['telephone', 'sameAs', 'geo', 'image', 'medicalSpecialty', 'availableService'] as $key) {
            $this->assertArrayNotHasKey($key, $block, $key);
        }
        $this->assertArrayNotHasKey('postalCode', $block['address']);
        $this->assertSame('100 Main Street', $block['address']['streetAddress'], 'no trailing space from an empty second line');
    }

    public function test_the_zero_zero_coordinate_is_not_output(): void
    {
        $clinic = $this->makeListing('Null Island Clinic', Listing::TYPE_CLINIC, [], ['latitude' => '0', 'longitude' => '0']);

        $this->assertArrayNotHasKey('geo', $this->one($this->listingPage($clinic), 'MedicalClinic'));
    }

    public function test_the_listing_coordinates_are_used_when_the_address_has_none(): void
    {
        $clinic = $this->makeListing('Fallback Clinic', Listing::TYPE_CLINIC, ['latitude' => '40.7128', 'longitude' => '-74.0060'], ['latitude' => '', 'longitude' => '']);

        $this->assertSame(
            ['@type' => 'GeoCoordinates', 'latitude' => 40.7128, 'longitude' => -74.006],
            $this->one($this->listingPage($clinic), 'MedicalClinic')['geo'],
        );
    }

    public function test_a_foreign_address_uses_the_country_code_and_the_state_name(): void
    {
        $clinic = $this->makeListing('London Clinic', Listing::TYPE_CLINIC, [], [
            'city' => 'London', 'state' => 'England', 'state_code' => '', 'country' => 'United Kingdom', 'country_code' => 'gb', 'postal_code' => 'W1G 9PF',
        ]);

        $address = $this->one($this->listingPage($clinic), 'MedicalClinic')['address'];

        $this->assertSame('England', $address['addressRegion']);
        $this->assertSame('GB', $address['addressCountry']);
        $this->assertSame('W1G 9PF', $address['postalCode']);
    }

    public function test_the_website_becomes_a_same_as_link_only_when_it_is_a_real_address(): void
    {
        $withScheme = $this->makeListing('One Clinic', Listing::TYPE_CLINIC, ['website' => 'http://one.example.com/about']);
        $junk = $this->makeListing('Two Clinic', Listing::TYPE_CLINIC, ['website' => 'not a website']);
        $script = $this->makeListing('Three Clinic', Listing::TYPE_CLINIC, ['website' => 'javascript:alert(1)']);

        $this->assertSame(['http://one.example.com/about'], $this->one($this->listingPage($withScheme), 'MedicalClinic')['sameAs']);
        $this->assertArrayNotHasKey('sameAs', $this->one($this->listingPage($junk), 'MedicalClinic'));
        $this->assertArrayNotHasKey('sameAs', $this->one($this->listingPage($script), 'MedicalClinic'));
    }

    public function test_the_clinic_breadcrumbs_are_home_marketplace_and_the_clinic(): void
    {
        $clinic = $this->makeListing();

        $crumbs = $this->one($this->listingPage($clinic), 'BreadcrumbList');

        $this->assertSame([
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Longevity Marketplace', 'item' => url('/marketplace')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => 'Alpha Longevity Clinic', 'item' => url('/marketplace/clinic/alpha-longevity-clinic')],
        ], $crumbs['itemListElement']);
    }

    public function test_a_script_closing_tag_in_a_clinic_name_is_safely_escaped(): void
    {
        $clinic = $this->makeListing(self::EVIL);

        $html = $this->listingPage($clinic);

        $this->assertSame(self::EVIL, $this->one($html, 'MedicalClinic')['name']);
        $this->assertSame(['MedicalClinic', 'BreadcrumbList'], $this->types($html));
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    /* ------------------------------------------------------------------ doctor listings */

    public function test_a_doctor_page_has_one_physician_and_one_breadcrumb_block_and_no_clinic_block(): void
    {
        $doctor = $this->makeListing('Dr Jane Doe', Listing::TYPE_DOCTOR);

        $html = $this->listingPage($doctor);

        $this->assertSame(['Physician', 'BreadcrumbList'], $this->types($html));
        $this->assertNoRatings($html);
    }

    public function test_a_full_doctor_has_every_requested_property(): void
    {
        $doctor = $this->makeListing('Dr Jane Doe', Listing::TYPE_DOCTOR, ['website' => 'drjanedoe.com']);
        $this->tag('Geriatrics', 'Clinic Focus', $doctor);
        $this->tag('Endocrinology', 'Clinic Focus', $doctor);

        $block = $this->one($this->listingPage($doctor), 'Physician');

        $this->assertSame('Dr Jane Doe', $block['name']);
        $this->assertSame(url('/marketplace/doctor/dr-jane-doe'), $block['url']);
        $this->assertSame('PostalAddress', $block['address']['@type']);
        $this->assertSame('Miami', $block['address']['addressLocality']);
        $this->assertSame(['Endocrinology', 'Geriatrics'], $block['medicalSpecialty']);
        $this->assertSame(['https://drjanedoe.com'], $block['sameAs']);
        $this->assertArrayNotHasKey('memberOf', $block, 'no clinic is linked');
    }

    public function test_a_doctor_links_to_the_clinic_it_is_connected_to(): void
    {
        $doctor = $this->makeListing('Dr Jane Doe', Listing::TYPE_DOCTOR);
        $clinic = $this->makeListing('Alpha Longevity Clinic');
        $this->connect($doctor, $clinic);

        $this->assertSame(
            ['@type' => 'MedicalClinic', 'name' => 'Alpha Longevity Clinic', 'url' => url('/marketplace/clinic/alpha-longevity-clinic')],
            $this->one($this->listingPage($doctor), 'Physician')['memberOf'],
        );
    }

    public function test_a_doctor_also_links_to_a_clinic_that_lists_the_doctor(): void
    {
        $doctor = $this->makeListing('Dr Jane Doe', Listing::TYPE_DOCTOR);
        $clinic = $this->makeListing('Alpha Longevity Clinic');
        $this->connect($clinic, $doctor, 'Doctor');

        $this->assertSame('Alpha Longevity Clinic', $this->one($this->listingPage($doctor), 'Physician')['memberOf']['name']);
    }

    public function test_only_public_clinics_count_as_the_doctors_clinic(): void
    {
        $doctor = $this->makeListing('Dr Jane Doe', Listing::TYPE_DOCTOR);
        $pending = $this->makeListing('Pending Clinic', Listing::TYPE_CLINIC, ['status' => Listing::STATUS_PENDING]);
        $otherDoctor = $this->makeListing('Dr John Roe', Listing::TYPE_DOCTOR);
        $this->connect($doctor, $pending);
        $this->connect($doctor, $otherDoctor);

        $this->assertArrayNotHasKey('memberOf', $this->one($this->listingPage($doctor), 'Physician'), 'not a pending clinic, not another doctor');
    }

    public function test_a_doctor_at_two_clinics_lists_both_alphabetically(): void
    {
        $doctor = $this->makeListing('Dr Jane Doe', Listing::TYPE_DOCTOR);
        $zulu = $this->makeListing('Zulu Clinic');
        $alpha = $this->makeListing('Alpha Clinic');
        $this->connect($doctor, $zulu);
        $this->connect($alpha, $doctor);

        $clinics = $this->one($this->listingPage($doctor), 'Physician')['memberOf'];

        $this->assertSame(['Alpha Clinic', 'Zulu Clinic'], array_column($clinics, 'name'));
    }

    public function test_a_doctor_with_only_a_name_has_no_other_property(): void
    {
        $doctor = $this->makeListing('Dr Bare', Listing::TYPE_DOCTOR, [], null);

        $html = $this->listingPage($doctor);

        $this->assertSame(['@context', '@type', 'name', 'url'], array_keys($this->one($html, 'Physician')));
        foreach ($this->blocks($html) as $block) {
            $this->assertNoEmptyValues($block);
        }
    }

    public function test_the_doctor_breadcrumbs_are_home_marketplace_and_the_doctor(): void
    {
        $doctor = $this->makeListing('Dr Jane Doe', Listing::TYPE_DOCTOR);

        $crumbs = $this->one($this->listingPage($doctor), 'BreadcrumbList');

        $this->assertSame(['Home', 'Longevity Marketplace', 'Dr Jane Doe'], array_column($crumbs['itemListElement'], 'name'));
        $this->assertSame(url('/marketplace/doctor/dr-jane-doe'), $crumbs['itemListElement'][2]['item']);
    }

    public function test_a_script_closing_tag_in_a_doctor_name_is_safely_escaped(): void
    {
        $doctor = $this->makeListing(self::EVIL, Listing::TYPE_DOCTOR);

        $html = $this->listingPage($doctor);

        $this->assertSame(self::EVIL, $this->one($html, 'Physician')['name']);
        $this->assertSame(['Physician', 'BreadcrumbList'], $this->types($html));
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function test_coach_and_retreat_pages_print_no_json_ld(): void
    {
        foreach ([Listing::TYPE_COACH, Listing::TYPE_RETREAT] as $type) {
            $listing = $this->makeListing('Some ' . $type, $type);

            $this->assertSame([], $this->blocks($this->listingPage($listing)), $type);
        }
    }

    /* ------------------------------------------------------------------ homepage */

    private function homepage(?string $storedSeo = '<meta name="description" content="Spannr home" />'): string
    {
        DB::table('content')->insert([
            'name' => 'home', 'slug' => 'home', 'status' => 'Published', 'seo' => $storedSeo, 'created_at' => now(), 'updated_at' => now(),
        ]);

        return $this->get('/')->assertOk()->getContent();
    }

    public function test_the_homepage_has_one_organization_block(): void
    {
        $html = $this->homepage();

        $this->assertSame(['Organization'], $this->types($html));
        $org = $this->one($html, 'Organization');
        $this->assertSame('https://schema.org', $org['@context']);
        $this->assertSame('Spannr', $org['name']);
        $this->assertSame(url('/'), $org['url']);
        $this->assertSame(['@type' => 'ImageObject', 'url' => 'https://spannr.test/build/assets/logo-color.png'], $org['logo']);
        $this->assertSame(['@type' => 'Person', 'name' => 'Cody Shirk'], $org['founder']);
        $this->assertNoRatings($html);
    }

    public function test_the_organization_same_as_links_are_exactly_the_footer_social_links(): void
    {
        $html = $this->homepage();
        $org = $this->one($html, 'Organization');

        $footer = view('public.includes.footer')->render();
        preg_match_all('#<div class="social-icons">(.*?)</footer>#s', $footer, $block);
        preg_match_all('#href="([^"]+)"#', $block[1][0], $links);

        $this->assertNotEmpty($links[1]);
        $this->assertSame($links[1], $org['sameAs'], 'the JSON-LD and the footer cannot drift apart');
        $this->assertSame(StructuredData::SOCIAL_PROFILES, $org['sameAs']);
    }

    public function test_an_organization_already_in_the_stored_head_html_is_not_duplicated(): void
    {
        $stored = '<script type="application/ld+json">{"@context":"https://schema.org","@graph":[{"@type":"Organization","name":"Spannr"}]}</script>';

        $html = $this->homepage($stored);

        $this->assertSame(['(graph)'], $this->types($html), 'only the stored block');
        $this->assertSame(1, substr_count($html, 'application/ld+json'));
        $this->assertStringNotContainsString('Cody Shirk', $html);
    }

    public function test_other_stored_json_ld_does_not_suppress_the_organization(): void
    {
        $stored = '<script type="application/ld+json">{"@context":"https://schema.org","@type":"WebSite","name":"Spannr"}</script>';

        $html = $this->homepage($stored);

        $types = $this->types($html);
        sort($types);
        $this->assertSame(['Organization', 'WebSite'], $types);
        $this->assertSame('Cody Shirk', $this->one($html, 'Organization')['founder']['name']);
    }

    public function test_the_organization_is_only_on_the_homepage(): void
    {
        $this->makeArticle();
        $this->makeGlossary();
        $clinic = $this->makeListing();

        foreach ([$this->articlePage(), $this->get('/glossary/nad')->getContent(), $this->listingPage($clinic)] as $html) {
            $this->assertSame([], $this->ofType($html, 'Organization'));
            $this->assertStringNotContainsString('Cody Shirk', $html);
        }
    }

    /* ------------------------------------------------------------------ the hand-set date, shown to readers */

    private function authorBox(Article $article): string
    {
        return view('public.content.articles._author-box', ['article' => $article->fresh()->load('authors')])->render();
    }

    public function test_the_author_box_shows_updated_only_when_the_hand_set_date_is_set(): void
    {
        $this->makeArticle(['content_updated_at' => null]);
        $this->assertStringNotContainsString('Updated:', $this->authorBox(Article::first()));
        $this->assertStringNotContainsString('Updated:', $this->articlePage());

        DB::table('articles')->update(['content_updated_at' => '2026-03-05']);
        $this->assertStringContainsString('<div class="article-byline-updated">Updated: March 5, 2026</div>', $this->authorBox(Article::first()));
        $this->assertStringContainsString('Updated: March 5, 2026', $this->articlePage());
    }

    public function test_the_updated_line_ignores_updated_at(): void
    {
        $this->makeArticle(['content_updated_at' => null, 'updated_at' => '2026-09-01 12:00:00']);

        $this->assertStringNotContainsString('Updated:', $this->authorBox(Article::first()));
        $this->assertStringNotContainsString('September 1, 2026', $this->articlePage());
    }

    public function test_the_updated_line_follows_the_author_line_and_leaves_the_reviewer_lines_alone(): void
    {
        $article = $this->makeArticle([
            'content_updated_at' => '2026-03-05', 'reviewer_name' => 'Sam Patel', 'reviewer_title' => 'Registered Dietitian', 'reviewed_at' => '2026-02-01',
        ]);

        $box = preg_replace('/\s+/', ' ', strip_tags($this->authorBox($article)));

        $this->assertSame(
            'By Jordan Rivera, Staff Writer Updated: March 5, 2026 Reviewed by Sam Patel, Registered Dietitian Last reviewed: February 1, 2026',
            trim($box),
        );
    }

    public function test_the_updated_line_shows_even_with_no_author_and_no_reviewer(): void
    {
        $article = $this->makeArticle(['content_updated_at' => '2026-03-05'], null);

        $this->assertStringContainsString('Updated: March 5, 2026', $this->authorBox($article));
    }

    public function test_the_hand_set_date_is_cast_to_a_date(): void
    {
        $article = $this->makeArticle(['content_updated_at' => '2026-03-05']);

        $this->assertInstanceOf(Carbon::class, $article->content_updated_at);
        $this->assertSame('2026-03-05', $article->content_updated_at->toDateString());
        $this->assertNull($this->makeArticle(['slug' => 'other', 'name' => 'Other'])->content_updated_at);
    }

    public function test_both_article_requests_accept_a_date_or_nothing_and_reject_anything_else(): void
    {
        foreach ([\App\Http\Requests\Admin\Articles\StoreArticleRequest::class, \App\Http\Requests\Admin\Articles\UpdateArticleRequest::class] as $class) {
            $rules = ['content_updated_at' => (new $class)->rules()['content_updated_at']];

            $this->assertTrue(Validator::make(['content_updated_at' => '2026-03-05'], $rules)->passes(), $class);
            $this->assertTrue(Validator::make(['content_updated_at' => null], $rules)->passes(), $class);
            $this->assertTrue(Validator::make([], $rules)->passes(), $class . ' (field omitted)');
            $this->assertTrue(Validator::make(['content_updated_at' => 'not a date'], $rules)->fails(), $class);
        }
    }

    /* ------------------------------------------------------------------ the fragment link */

    public function test_the_all_longevity_doctors_by_city_link_goes_to_the_city_index_section(): void
    {
        $clinic = $this->makeListing();
        $doctor = $this->makeListing('Dr Jane Doe', Listing::TYPE_DOCTOR);

        foreach ([$clinic, $doctor] as $listing) {
            $this->assertStringContainsString(
                '<a href="' . url('/articles/longevity-doctors') . '#longevity-doctors-by-city">All longevity doctors by city</a>',
                $this->listingPage($listing),
            );
        }
    }

    public function test_the_fragment_is_the_id_of_the_city_index_section(): void
    {
        $partial = file_get_contents(resource_path('views/public/content/articles/_city-index.blade.php'));

        $this->assertStringContainsString('id="longevity-doctors-by-city"', $partial);
    }

    /* ------------------------------------------------------------------ the migration */

    public function test_the_migration_adds_a_nullable_date_column_and_down_drops_it(): void
    {
        $migration = $this->contentUpdatedAtMigration();

        $this->assertTrue(Schema::hasColumn('articles', 'content_updated_at'), 'up() ran in setUp');
        $this->assertSame('date', Schema::getColumnType('articles', 'content_updated_at'));

        $migration->down();
        $this->assertFalse(Schema::hasColumn('articles', 'content_updated_at'));
        $this->assertTrue(Schema::hasColumn('articles', 'reviewer_name'), 'the DEV-7 columns are untouched');
        $this->assertTrue(Schema::hasColumn('articles', 'name'));

        $migration->up();
        $this->assertTrue(Schema::hasColumn('articles', 'content_updated_at'));
    }

    public function test_the_migration_leaves_existing_rows_empty(): void
    {
        $this->contentUpdatedAtMigration()->down();
        $this->makeArticle();
        $this->contentUpdatedAtMigration()->up();

        $this->assertNull(DB::table('articles')->first()->content_updated_at);
    }
}
