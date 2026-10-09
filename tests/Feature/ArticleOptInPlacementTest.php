<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * DEV-13. The mid-article newsletter box is rendered once at the bottom of the article page and moved up into the
 * body by resources/js/post-optin-placement.mjs (tested by `npm run test:js`). These tests cover the server side the
 * script relies on, and that app.js uses the new placement code instead of the old "every paragraph" selector.
 * The tables are created here (in-memory SQLite from phpunit.xml) so no real database is touched.
 */
class ArticleOptInPlacementTest extends TestCase
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

    private function articleHtml(string $content): string
    {
        $now = now()->toDateTimeString();

        DB::table('articles')->insert([
            'date' => $now, 'name' => 'Sleep and Longevity', 'slug' => 'sleep-and-longevity', 'status' => 'publish',
            'excerpt' => 'x', 'content' => $content, 'created_at' => $now, 'updated_at' => $now,
        ]);

        return $this->get('/articles/sleep-and-longevity')->assertOk()->getContent();
    }

    private function tableHeavyBody(): string
    {
        return '<p>An opening paragraph that is long enough to count as real body text.</p>'
            . '<table><tbody><tr><td><p>A paragraph inside a table cell that is long enough to count.</p></td></tr></tbody></table>'
            . '<ul><li><p>A paragraph inside a list item that is long enough to count.</p></li></ul>';
    }

    public function test_the_page_renders_the_newsletter_box_exactly_once(): void
    {
        $html = $this->articleHtml($this->tableHeavyBody());

        $this->assertSame(1, substr_count($html, 'id="post-newsletter"'));
        $this->assertSame(1, substr_count($html, '<div id="post-newsletter" class="outline-newsletter-optin horizontal mb-4">'));
    }

    public function test_the_box_is_rendered_after_the_article_so_it_stays_at_the_bottom_if_the_script_finds_nowhere_to_put_it(): void
    {
        $html = $this->articleHtml('<table><tbody><tr><td><p>Only a table paragraph, and it is long enough to count.</p></td></tr></tbody></table>');

        $this->assertGreaterThan(strpos($html, '</article>'), strpos($html, 'id="post-newsletter"'));
        $this->assertGreaterThan(strpos($html, 'class="after-single-post"'), strpos($html, 'id="post-newsletter"'));
    }

    public function test_the_markup_the_script_looks_for_is_on_the_page_and_the_box_is_not_inside_it(): void
    {
        $html = $this->articleHtml($this->tableHeavyBody());

        $this->assertStringContainsString('<article class="single-post-content article-full-width pb-6">', $html);
        $this->assertSame(1, substr_count($html, '<div class="post-content">'));
        $this->assertStringContainsString('<table>', $html);
        $body = substr($html, strpos($html, '<div class="post-content">'), strpos($html, '</article>') - strpos($html, '<div class="post-content">'));
        $this->assertStringNotContainsString('post-newsletter', $body, 'before the script runs, the box is not inside the body');
    }

    public function test_app_js_uses_the_new_placement_code_and_not_the_old_every_paragraph_selector(): void
    {
        $app = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString("import { placePostOptIn } from './post-optin-placement.mjs';", $app);
        $this->assertStringContainsString("placePostOptIn(document.getElementById('post-newsletter'), document.querySelector('.single-post-content .post-content'));", $app);
        $this->assertStringNotContainsString("querySelectorAll('.single-post-content p')", $app);
        $this->assertStringNotContainsString('paragraphs[18]', $app);
        $this->assertSame(1, substr_count($app, 'post-newsletter'), 'one place moves the box');
    }

    public function test_the_placement_rules_are_in_the_module(): void
    {
        $module = file_get_contents(resource_path('js/post-optin-placement.mjs'));

        $this->assertStringContainsString("export const SKIP_INSIDE = 'table, ul, ol, blockquote, figure, details, .accordion, .collapse';", $module);
        $this->assertStringContainsString('export const MIN_TEXT_LENGTH = 40;', $module);
        $this->assertStringContainsString('paragraph.nextElementSibling !== box', $module);
        $this->assertStringContainsString('export function pickAnchor(', $module);
        $this->assertFileExists(base_path('tests/js/post-optin-placement.test.mjs'));
        $this->assertStringContainsString('"test:js": "node --test tests/js/*.test.mjs"', file_get_contents(base_path('package.json')));
    }
}
