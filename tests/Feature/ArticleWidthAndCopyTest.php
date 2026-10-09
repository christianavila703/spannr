<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * DEV-12. Article pages put the title, byline box, hero image and body in one centered 800px column and center
 * their images; the header Subscribe button and modal use the "Join the Insiders" copy.
 * The tables are created here (in-memory SQLite from phpunit.xml) so no real database is touched.
 */
class ArticleWidthAndCopyTest extends TestCase
{
    private const MODAL_LINE = "What's working. What's hype. What we're trying ourselves.";

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

    private function articleHtml(array $attributes = []): string
    {
        $now = now()->toDateTimeString();
        $authorId = DB::table('users')->insertGetId(['name' => 'Jordan Rivera', 'slug' => 'jordan-rivera', 'title' => 'Staff Writer', 'created_at' => $now, 'updated_at' => $now]);
        $id = DB::table('articles')->insertGetId(array_merge([
            'date' => $now, 'name' => 'Sleep and Longevity', 'slug' => 'sleep-and-longevity', 'status' => 'publish', 'excerpt' => 'x',
            'content' => '<p>Body text of the article.</p>', 'image' => 'hero.jpg', 'created_at' => $now, 'updated_at' => $now,
        ], $attributes));
        DB::table('article_user')->insert(['article_id' => $id, 'user_id' => $authorId, 'created_at' => $now, 'updated_at' => $now]);

        return $this->get('/articles/sleep-and-longevity')->assertOk()->getContent();
    }

    private function modalHtml(string $html): string
    {
        $start = strpos($html, 'id="header-subscribe-modal"');

        return $start === false ? '' : substr($html, $start, 2600);
    }

    public function test_title_byline_hero_and_article_share_one_column(): void
    {
        $html = $this->articleHtml();

        $column = strpos($html, '<div class="article-column">');
        $this->assertNotFalse($column, 'the column wrapper is on the article page');

        $article = strpos($html, '<article class="single-post-content article-full-width pb-6">');
        $articleEnd = strpos($html, '</article>', $article);
        $columnEnd = strpos($html, '<div class="after-single-post">');

        foreach (['<h1 class="post-title mb-4">', 'class="article-byline-box', 'class="mb-3 w-100"', '<article class="single-post-content article-full-width pb-6">'] as $inside) {
            $pos = strpos($html, $inside);
            $this->assertNotFalse($pos, $inside);
            $this->assertGreaterThan($column, $pos, "$inside comes after the column opens");
            $this->assertLessThan($columnEnd, $pos, "$inside is inside the column");
        }
        $this->assertLessThan($columnEnd, $articleEnd);
        $this->assertGreaterThan(strpos($html, '</article>'), strpos($html, 'class="after-single-post"'));
    }

    public function test_the_article_element_still_carries_the_full_width_class_and_the_wrapper_is_balanced(): void
    {
        $html = $this->articleHtml();
        $page = substr($html, strpos($html, '<div class="single-post-wrapper">'), strpos($html, '<div id="post-newsletter"') - strpos($html, '<div class="single-post-wrapper">'));

        $this->assertStringContainsString('<article class="single-post-content article-full-width pb-6">', $html);
        $this->assertSame(substr_count($page, '<div'), substr_count($page, '</div>'), 'every div opened in the article area is closed');
    }

    public function test_a_page_that_is_not_an_article_gets_neither_width_class(): void
    {
        $html = $this->get('/glossary/nad')->assertOk()->getContent();

        $this->assertStringNotContainsString('article-column', $html);
        $this->assertStringNotContainsString('article-full-width', $html);
    }

    public function test_the_header_button_says_join_the_insiders_and_keeps_the_envelope_icon(): void
    {
        $html = $this->articleHtml();

        $this->assertMatchesRegularExpression(
            '#<button[^>]*data-bs-target="\#header-subscribe-modal"[^>]*>\s*<i class="fa-sharp fa-light fa-envelope me-1"></i>\s*<span>Join the Insiders</span>\s*</button>#',
            $html,
        );
        $this->assertStringNotContainsString('<span>Subscribe</span>', $html);
    }

    public function test_the_header_modal_has_the_new_title_and_the_new_line_directly_under_it(): void
    {
        $modal = $this->modalHtml($this->articleHtml());

        $this->assertStringContainsString('id="header-subscribe-modal-label">The longevity insider email, every Thursday</div>', $modal);
        $this->assertStringContainsString('<p class="small text-muted mb-3">' . self::MODAL_LINE . '</p>', $modal);
        $this->assertLessThan(strpos($modal, self::MODAL_LINE), strpos($modal, 'The longevity insider email, every Thursday'), 'the line follows the title');
    }

    public function test_the_old_modal_title_is_gone_from_the_header_modal(): void
    {
        $modal = $this->modalHtml($this->articleHtml());

        $this->assertStringNotContainsString('Sign Up For Our Newsletter', $modal);
        $this->assertStringNotContainsString('Sign Up For Our  Newsletter', $modal);
    }

    public function test_the_signup_form_and_the_bottom_of_page_block_are_unchanged(): void
    {
        $html = $this->articleHtml();
        $modal = $this->modalHtml($html);

        $this->assertStringContainsString('header-subscribe-form', $modal);
        $this->assertStringContainsString('id="post-newsletter"', $html);
        $this->assertStringContainsString('Sign Up For Our  Newsletter', $html, 'the bottom block keeps its own title');
    }

    public function test_the_new_copy_has_no_em_dash_or_en_dash(): void
    {
        $modal = $this->modalHtml($this->articleHtml());
        $header = substr($modal, 0, strpos($modal, 'header-subscribe-form'));

        $this->assertSame(0, preg_match('/[\x{2012}-\x{2015}]/u', $header));
    }

    public function test_the_header_copy_shows_on_other_pages_too(): void
    {
        $html = $this->get('/glossary/nad')->assertOk()->getContent();

        $this->assertStringContainsString('<span>Join the Insiders</span>', $html);
        $this->assertStringContainsString('The longevity insider email, every Thursday', $html);
        $this->assertStringContainsString(self::MODAL_LINE, $html);
    }

    public function test_the_footer_still_has_no_subscribe_call_to_action(): void
    {
        $html = $this->get('/glossary/nad')->assertOk()->getContent();
        $footer = substr($html, strpos($html, '<footer class="public-footer">'));
        $footer = substr($footer, 0, strpos($footer, '</footer>'));

        $this->assertStringNotContainsString('Subscribe', $footer);
        $this->assertStringNotContainsString('Join the Insiders', $footer);
    }

    public function test_the_column_and_image_css_is_in_the_posts_sass_file(): void
    {
        $scss = file_get_contents(resource_path('sass/_posts.scss'));

        $this->assertMatchesRegularExpression('/\.single-post \.article-column \{\s*width: 100%;\s*max-width: 800px;\s*margin-left: auto;\s*margin-right: auto;/', $scss);
        $this->assertMatchesRegularExpression('/\.featured-image-picture img \{\s*display: block;\s*margin-left: auto;\s*margin-right: auto;\s*max-width: 100%;\s*height: auto;/', $scss);
        // Body images: centered block, beating editor float / align / inline styles.
        $this->assertMatchesRegularExpression('/\bimg \{\s*display: block;\s*float: none !important;\s*margin-left: auto !important;\s*margin-right: auto !important;\s*max-width: 100% !important;\s*height: auto !important;/', $scss);
        foreach (['figure', '.wp-caption', '.alignleft', '.alignright', '.align-left', '.align-right'] as $selector) {
            $this->assertStringContainsString($selector . ',', $scss . ',', $selector);
        }
        $this->assertMatchesRegularExpression('/figcaption,\s*\.wp-caption-text,\s*\.image-caption-text \{\s*text-align: center !important;/', $scss);
        // DEV-11 table rules and the 600px column for other pages are still there.
        $this->assertMatchesRegularExpression('/table \{[^}]*width: 100% !important;[^}]*border-collapse: collapse;/s', $scss);
        $this->assertMatchesRegularExpression('/\.single-post \.single-post-content \{\s*width: 100%;\s*max-width: 600px;/', $scss);
        $this->assertSame(1, substr_count($scss, '.single-post .article-column {'));
    }
}
