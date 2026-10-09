<?php

namespace Tests\Feature;

use App\Support\CityGuideIndex;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * DEV-9. The "Longevity Doctors by City" block on /articles/longevity-doctors, and the footer link.
 * The tables are created here (in-memory SQLite from phpunit.xml) so no real database is touched.
 */
class CityGuideIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

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
        (require base_path('database/migrations/2026_10_08_120000_add_reviewer_fields_to_articles_table.php'))->up();

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
        Schema::create('article_user', function (Blueprint $table) {
            $table->unsignedBigInteger('article_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->timestamps();
        });
        Schema::create('taggables', function (Blueprint $table) {
            $table->unsignedBigInteger('tag_id');
            $table->morphs('taggable');
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
        Schema::create('glossary', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('group')->nullable();
            $table->string('status')->nullable();
            $table->mediumText('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();
        });
        DB::table('glossary')->insert(['name' => 'NAD', 'slug' => 'nad', 'group' => 'N', 'status' => 'publish', 'excerpt' => 'A coenzyme.', 'content' => 'x']);
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
        Schema::create('content', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('status')->default('Published');
            $table->longText('seo')->nullable();
            $table->longText('content')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('link')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->timestamps();
        });

        // The layout asks Vite for the built assets, which do not exist in a test run.
        $this->withoutVite();
    }

    private function article(string $slug, string $status = 'publish', string $content = '<p>Body text.</p>'): void
    {
        $now = now()->toDateTimeString();

        DB::table('articles')->insert([
            'date' => $now, 'name' => 'Guide ' . $slug, 'slug' => $slug, 'status' => $status,
            'excerpt' => 'x', 'content' => $content, 'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    /** A realistic mix: state-coded guides, older guides with no state, an unpublished guide, and an unrelated article. */
    private function seedGuides(): void
    {
        $this->article('longevity-doctors', content: '<p>National guide body.</p>');
        $this->article('longevity-doctors-scottsdale-az');
        $this->article('longevity-doctors-phoenix-az');
        $this->article('longevity-doctors-los-angeles-ca');
        $this->article('longevity-doctors-austin-tx');
        $this->article('longevity-doctors-new-york-city-ny');
        $this->article('longevity-doctors-miami-fl');
        $this->article('longevity-doctors-london');
        $this->article('longevity-doctors-boston');
        $this->article('longevity-doctors-denver-co', 'draft');
        $this->article('sleep-and-longevity');
    }

    private function indexHtml(string $html): string
    {
        $start = strpos($html, '<section class="city-guide-index');

        return $start === false ? '' : substr($html, $start, strpos($html, '</section>', $start) - $start + 10);
    }

    public function test_the_block_appears_on_the_national_guide_below_the_article_body(): void
    {
        $this->seedGuides();

        $response = $this->get('/articles/longevity-doctors');

        $response->assertOk();
        $html = $response->getContent();
        $this->assertStringContainsString('Longevity Doctors by City', $this->indexHtml($html));
        $this->assertStringContainsString('<h2 class="h3 mb-3" id="city-guide-index-title">Longevity Doctors by City</h2>', $html);
        $this->assertGreaterThan(strpos($html, 'National guide body.'), strpos($html, 'city-guide-index'), 'below the article body');
    }

    public function test_the_block_appears_on_no_other_article_not_even_a_city_guide(): void
    {
        $this->seedGuides();

        foreach (['/articles/sleep-and-longevity', '/articles/longevity-doctors-austin-tx', '/articles/longevity-doctors-london'] as $path) {
            $html = $this->get($path)->assertOk()->getContent();

            $this->assertStringNotContainsString('city-guide-index', $html, $path);
            $this->assertStringNotContainsString('Longevity Doctors by City</h2>', $html, $path);
        }
    }

    public function test_it_lists_published_guides_and_omits_an_unpublished_one(): void
    {
        $this->seedGuides();

        $block = $this->indexHtml($this->get('/articles/longevity-doctors')->getContent());

        foreach (['Scottsdale', 'Phoenix', 'Los Angeles', 'Austin', 'New York City', 'Miami', 'London', 'Boston'] as $city) {
            $this->assertStringContainsString('>' . $city . '</a>', $block, $city);
        }
        $this->assertStringNotContainsString('Denver', $block, 'its guide is a draft');
        $this->assertStringNotContainsString('longevity-doctors-denver-co', $block);
        $this->assertStringNotContainsString('Colorado', $block, 'a state with no published guide gets no heading');
    }

    public function test_it_does_not_invent_cities(): void
    {
        $this->seedGuides();

        $block = $this->indexHtml($this->get('/articles/longevity-doctors')->getContent());

        $this->assertSame(8, substr_count($block, '<li'), 'one entry per published city guide, nothing more');
        $this->assertStringNotContainsString('Seattle', $block);
        $this->assertStringNotContainsString('Chicago', $block);
    }

    public function test_states_and_cities_are_in_alphabetical_order(): void
    {
        $this->seedGuides();

        $block = $this->indexHtml($this->get('/articles/longevity-doctors')->getContent());

        $this->assertSame(
            ['Arizona', 'California', 'Florida', 'New York', 'Texas', 'Other cities'],
            array_map('strip_tags', $this->headings($block)),
        );
        $this->assertSeeInOrder($block, ['Arizona', 'Phoenix', 'Scottsdale', 'California', 'Los Angeles', 'Florida', 'Miami', 'New York', 'New York City', 'Texas', 'Austin', 'Other cities', 'Boston', 'London']);
    }

    /** @return array<int, string> */
    private function headings(string $block): array
    {
        preg_match_all('#<h3[^>]*>(.*?)</h3>#s', $block, $m);

        return $m[1];
    }

    private function assertSeeInOrder(string $haystack, array $needles): void
    {
        $offset = 0;
        foreach ($needles as $needle) {
            $pos = strpos($haystack, $needle, $offset);
            $this->assertNotFalse($pos, "'$needle' should appear after the previous item");
            $offset = $pos + strlen($needle);
        }
    }

    public function test_each_link_points_at_the_right_guide(): void
    {
        $this->seedGuides();

        $block = $this->indexHtml($this->get('/articles/longevity-doctors')->getContent());

        foreach ([
            'Scottsdale' => 'longevity-doctors-scottsdale-az',
            'Phoenix' => 'longevity-doctors-phoenix-az',
            'Los Angeles' => 'longevity-doctors-los-angeles-ca',
            'Austin' => 'longevity-doctors-austin-tx',
            'New York City' => 'longevity-doctors-new-york-city-ny',
            'Miami' => 'longevity-doctors-miami-fl',
            'London' => 'longevity-doctors-london',
            'Boston' => 'longevity-doctors-boston',
        ] as $city => $slug) {
            $this->assertStringContainsString('<a href="' . url('/articles/' . $slug) . '">' . $city . '</a>', $block, $city);
        }
    }

    public function test_the_page_still_returns_200_with_zero_city_guides_and_shows_no_empty_block(): void
    {
        $this->article('longevity-doctors', content: '<p>National guide body.</p>');

        $response = $this->get('/articles/longevity-doctors');

        $response->assertOk();
        $response->assertSee('National guide body.', false);
        $this->assertStringNotContainsString('city-guide-index', $response->getContent());
    }

    public function test_the_page_returns_200_when_only_drafts_exist(): void
    {
        $this->article('longevity-doctors');
        $this->article('longevity-doctors-denver-co', 'draft');

        $response = $this->get('/articles/longevity-doctors');

        $response->assertOk();
        $this->assertStringNotContainsString('city-guide-index', $response->getContent());
    }

    public function test_slug_parsing(): void
    {
        $this->assertSame(['Austin', 'Texas'], CityGuideIndex::parse('longevity-doctors-austin-tx'));
        $this->assertSame(['New York City', 'New York'], CityGuideIndex::parse('longevity-doctors-new-york-city-ny'));
        $this->assertSame(['Fort Lauderdale', 'Florida'], CityGuideIndex::parse('longevity-doctors-fort-lauderdale-fl'));
        $this->assertSame(['London', null], CityGuideIndex::parse('longevity-doctors-london'));
        $this->assertSame(['New York', null], CityGuideIndex::parse('longevity-doctors-new-york'), 'no state code, so no state is guessed');
        $this->assertSame([null, null], CityGuideIndex::parse('longevity-doctors'), 'the national guide is not a city');
        $this->assertSame([null, null], CityGuideIndex::parse('sleep-and-longevity'));
        $this->assertSame(['Xx', null], CityGuideIndex::parse('longevity-doctors-xx'), 'a lone token is a city, never a state');
    }

    public function test_the_footer_link_appears_on_normal_pages(): void
    {
        $this->seedGuides();

        foreach (['/articles/sleep-and-longevity', '/confirmed'] as $path) {
            $html = $this->get($path)->assertOk()->getContent();

            $this->assertStringContainsString(
                '<a href="' . url('/articles/longevity-doctors') . '" class="nav-link">Longevity Doctors by City</a>',
                $html,
                $path,
            );
        }
    }

    public function test_the_footer_link_sits_inside_the_footer_menu(): void
    {
        $html = $this->get('/confirmed')->getContent();

        $footer = substr($html, strpos($html, '<footer class="public-footer">'));
        $this->assertStringContainsString('Longevity Doctors by City', substr($footer, 0, strpos($footer, '</footer>')));
    }
}
