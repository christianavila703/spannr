<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * DEV-11. Article pages have no right-hand sidebar (no newsletter box, no random glossary term), the body uses the
 * full container width, and tables in the body are styled to fill it. Other pages keep their sidebar.
 * The tables are created here (in-memory SQLite from phpunit.xml) so no real database is touched.
 */
class ArticleLayoutTest extends TestCase
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
        DB::table('glossary')->insert([
            ['name' => 'NAD', 'slug' => 'nad', 'group' => 'N', 'status' => 'publish', 'excerpt' => 'A coenzyme.', 'content' => '<p>NAD body.</p>'],
            ['name' => 'Senolytics', 'slug' => 'senolytics', 'group' => 'S', 'status' => 'publish', 'excerpt' => 'Drugs that clear old cells.', 'content' => '<p>Senolytics body.</p>'],
        ]);
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

    private function article(string $slug = 'sleep-and-longevity', array $attributes = []): void
    {
        $now = now()->toDateTimeString();

        DB::table('articles')->insert(array_merge([
            'date' => $now, 'name' => 'Sleep and Longevity', 'slug' => $slug, 'status' => 'publish', 'excerpt' => 'x',
            'content' => '<p>Body text of the article.</p><table><tr><th>Habit</th><th>Effect</th></tr><tr><td>Sleep</td><td>Repair</td></tr></table>',
            'created_at' => $now, 'updated_at' => $now,
        ], $attributes));
    }

    private function articleHtml(array $attributes = []): string
    {
        $this->article('sleep-and-longevity', $attributes);

        return $this->get('/articles/sleep-and-longevity')->assertOk()->getContent();
    }

    public function test_the_article_page_has_no_sidebar_column(): void
    {
        $html = $this->articleHtml();

        $this->assertStringNotContainsString('class="sidebar-container"', $html);
        $this->assertStringNotContainsString('id="sidebar"', $html);
        $this->assertStringNotContainsString('id="sticky-sidebar-contents"', $html);
        $this->assertStringNotContainsString('id="sticky-stabilizer"', $html);
    }

    public function test_the_article_page_no_longer_renders_the_sidebar_newsletter_box(): void
    {
        $html = $this->articleHtml();

        // The sidebar box asked for first and last name. The one-line box at the bottom of the page and the header form do not.
        $this->assertStringNotContainsString('placeholder="First name"', $html);
        $this->assertStringNotContainsString('placeholder="Last name"', $html);
        // The bottom-of-page signup is deliberately kept: exactly one newsletter box is left.
        $this->assertSame(1, substr_count($html, 'class="sidebar-optin"'));
        $this->assertStringContainsString('id="post-newsletter"', $html);
    }

    public function test_the_article_page_no_longer_renders_the_random_glossary_widget(): void
    {
        $html = $this->articleHtml();

        $this->assertStringNotContainsString('Random Glossary Term', $html);
        $this->assertStringNotContainsString('sidebar-glossary-widget', $html);
    }

    public function test_the_sticky_sidebar_script_and_jquery_are_gone_from_the_article_page(): void
    {
        $html = $this->articleHtml();

        $this->assertStringNotContainsString('sticky-sidebar', $html);
        $this->assertStringNotContainsString('code.jquery.com', $html);
    }

    public function test_the_article_body_wrapper_is_still_present_and_full_width(): void
    {
        $html = $this->articleHtml();

        $this->assertStringContainsString('<article class="single-post-content article-full-width pb-6">', $html);
        $this->assertStringContainsString('<div class="post-content">', $html);
        $this->assertStringContainsString('Body text of the article.', $html);
        $this->assertStringContainsString('<table>', $html, 'the editor\'s table is still in the body');
        $this->assertStringContainsString('class="single-post-content-sidebar-container"', $html);
        $this->assertStringContainsString('class="after-single-post"', $html);
    }

    public function test_the_cms_sidebar_field_is_kept_below_the_body_when_an_article_has_one(): void
    {
        $html = $this->articleHtml(['sidebar' => '<p>Key takeaways from the CMS.</p>']);

        $this->assertStringContainsString('<div class="article-extra mt-5"><p>Key takeaways from the CMS.</p></div>', $html);
        $this->assertGreaterThan(strpos($html, 'Body text of the article.'), strpos($html, 'Key takeaways from the CMS.'));
        $this->assertStringNotContainsString('class="sidebar-container"', $html);
    }

    public function test_an_article_without_a_cms_sidebar_renders_no_empty_block(): void
    {
        $this->assertStringNotContainsString('article-extra', $this->articleHtml());
    }

    public function test_the_sidebar_is_also_gone_on_the_national_guide_and_the_page_is_still_200(): void
    {
        $this->article('longevity-doctors');
        $this->article('longevity-doctors-austin-tx');

        $html = $this->get('/articles/longevity-doctors')->assertOk()->getContent();

        $this->assertStringNotContainsString('Random Glossary Term', $html);
        $this->assertStringNotContainsString('id="sidebar"', $html);
        $this->assertStringContainsString('city-guide-index', $html, 'the city index from DEV-9 is untouched');
    }

    public function test_the_glossary_page_still_renders_its_sidebar_with_the_newsletter_box_and_glossary_widget(): void
    {
        $html = $this->get('/glossary/nad')->assertOk()->getContent();

        $this->assertStringContainsString('class="sidebar-container"', $html);
        $this->assertStringContainsString('id="sticky-sidebar-contents"', $html);
        $this->assertStringContainsString('Random Glossary Term', $html);
        $this->assertStringContainsString('sidebar-glossary-widget', $html);
        $this->assertStringContainsString('placeholder="First name"', $html, 'the sidebar newsletter box with name fields');
        $this->assertStringNotContainsString('article-full-width', $html, 'the full-width class is for articles only');
    }

    public function test_the_table_and_full_width_css_is_in_the_existing_posts_sass_file_and_scoped_to_articles(): void
    {
        $scss = file_get_contents(resource_path('sass/_posts.scss'));

        $this->assertStringContainsString('.single-post .single-post-content.article-full-width {', $scss);
        $this->assertMatchesRegularExpression('/article-full-width \{\s*max-width: 100%;/', $scss);
        $this->assertMatchesRegularExpression('/table \{[^}]*width: 100%[^}]*border-collapse: collapse;/s', $scss);
        $this->assertMatchesRegularExpression('/th,\s*td \{[^}]*padding:[^}]*text-align: left;/s', $scss);
        $this->assertMatchesRegularExpression('/th,\s*thead td \{[^}]*font-weight: 700;[^}]*background-color:/s', $scss);
        $this->assertMatchesRegularExpression('/max-width: \$bp-min-tablet - 1px\) \{\s*table \{[^}]*overflow-x: auto;/s', $scss);
        $this->assertFileDoesNotExist(resource_path('sass/_article-tables.scss'));
        // The old 600px column for every other long-form page is still there.
        $this->assertMatchesRegularExpression('/\.single-post \.single-post-content \{\s*width: 100%;\s*max-width: 600px;/', $scss);
    }

    public function test_the_shared_pieces_are_not_deleted(): void
    {
        $this->assertFileExists(resource_path('views/components/optins/newsletter.blade.php'));
        $this->assertFileExists(resource_path('views/livewire/public/random-glossary-item-widget.blade.php'));
        $this->assertFileExists(app_path('Http/Livewire/Public/RandomGlossaryItemWidget.php'));
        $this->assertFileExists(resource_path('js/sticky-sidebar.js'));
    }
}
