<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * DEV-7. The author / reviewer box under the article headline.
 * The tables are created here (in-memory SQLite from phpunit.xml) so no real database is touched.
 */
class ArticleAuthorBoxTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        $this->createTables();

        // The layout asks Vite for the built assets, which do not exist in a test run.
        $this->withoutVite();
    }

    private function createTables(): void
    {
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
        // The real migration adds the three reviewer columns, so every test here runs on top of it.
        $this->reviewerMigration()->up();

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
        // The article page shows a random glossary term in the sidebar.
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
    }

    private function reviewerMigration(): object
    {
        return require base_path('database/migrations/2026_10_08_120000_add_reviewer_fields_to_articles_table.php');
    }

    /**
     * @param array<string, mixed> $article
     * @param array<string, mixed>|null $author null = an article with no author
     */
    private function makeArticle(array $article = [], ?array $author = ['name' => 'Jordan Rivera', 'slug' => 'jordan-rivera', 'title' => 'Staff Writer']): Article
    {
        $now = now()->toDateTimeString();

        $id = DB::table('articles')->insertGetId(array_merge([
            'date' => $now,
            'name' => 'Sleep and Longevity',
            'slug' => 'sleep-and-longevity',
            'status' => 'publish',
            'excerpt' => 'x',
            'content' => '<p>Body text of the article.</p>',
            'created_at' => $now,
            'updated_at' => $now,
        ], $article));

        if ($author !== null) {
            $userId = DB::table('users')->insertGetId(array_merge(['created_at' => $now, 'updated_at' => $now], $author));
            DB::table('article_user')->insert(['article_id' => $id, 'user_id' => $userId, 'created_at' => $now, 'updated_at' => $now]);
        }

        return Article::findOrFail($id);
    }

    private function boxHtml(string $html): string
    {
        $start = strpos($html, '<div class="article-byline-box');

        return $start === false ? '' : substr($html, $start, 1500);
    }

    public function test_the_box_shows_the_author_under_the_headline_and_links_to_their_page(): void
    {
        $article = $this->makeArticle();

        $response = $this->get('/articles/sleep-and-longevity');

        $response->assertOk();
        $html = $response->getContent();
        $this->assertStringContainsString('class="article-byline-box', $html);
        $this->assertLessThan(strpos($html, 'article-byline-box'), strpos($html, '<h1 class="post-title'), 'box comes after the headline');
        $this->assertLessThan(strpos($html, 'Body text of the article.'), strpos($html, 'article-byline-box'), 'box comes before the body');

        // Directly under the headline: only whitespace sits between the closing </h1> and the box.
        $between = substr($html, strpos($html, '</h1>') + 5, strpos($html, '<div class="article-byline-box') - strpos($html, '</h1>') - 5);
        $this->assertSame('', trim($between), 'nothing but whitespace between the headline and the box');

        $box = $this->boxHtml($html);
        $this->assertStringContainsString('By ', $box);
        $this->assertStringContainsString('<a href="' . url('/about/jordan-rivera') . '"', $box);
        $this->assertStringContainsString('Jordan Rivera</a>', $box);
        $this->assertStringContainsString('Staff Writer', $box, 'the role comes from the existing User title field');
    }

    public function test_the_visible_name_is_the_same_value_the_json_ld_uses(): void
    {
        $this->makeArticle([], ['name' => 'Jordan Rivera', 'byline' => 'Dr. J. Rivera', 'slug' => 'jordan-rivera']);

        $html = $this->get('/articles/sleep-and-longevity')->getContent();
        $box = $this->boxHtml($html);

        $this->assertStringContainsString('Dr. J. Rivera</a>', $box);
        $this->assertSame('Dr. J. Rivera', Article::first()->author_byline);
        // The JSON-LD author name is author_byline too (Article::getDynamicSEOData), so the two cannot drift apart.
        $this->assertStringContainsString('"name":"Dr. J. Rivera"', str_replace(['\\/', ' "name"'], ['/', '"name"'], $html));
    }

    public function test_an_author_without_a_slug_is_shown_as_plain_text_not_a_broken_link(): void
    {
        // Rendered on its own: the older "By" line and the bottom "About the Author" box call route() with
        // the slug and would throw for such an author. That is existing behaviour and is not changed here.
        $article = $this->makeArticle([], ['name' => 'Jordan Rivera', 'byline' => 'Dr. J. Rivera', 'slug' => null])->load('authors');

        $box = view('public.content.articles._author-box', ['article' => $article])->render();

        $this->assertStringContainsString('Dr. J. Rivera', $box, 'same value as the JSON-LD author name');
        $this->assertStringNotContainsString('<a ', $box);
    }

    public function test_reviewer_line_and_date_appear_only_when_set(): void
    {
        $this->makeArticle([
            'reviewer_name' => 'Sam Patel',
            'reviewer_title' => 'Registered Dietitian',
            'reviewed_at' => '2026-03-05',
        ]);

        $box = $this->boxHtml($this->get('/articles/sleep-and-longevity')->getContent());

        $this->assertStringContainsString('Reviewed by', $box);
        $this->assertStringContainsString('Sam Patel', $box);
        $this->assertStringContainsString('Registered Dietitian', $box);
        $this->assertStringContainsString('Last reviewed: March 5, 2026', $box);
    }

    public function test_a_date_alone_shows_last_reviewed_but_no_reviewer_line(): void
    {
        $this->makeArticle(['reviewed_at' => '2026-03-05']);

        $box = $this->boxHtml($this->get('/articles/sleep-and-longevity')->getContent());

        $this->assertStringContainsString('Last reviewed: March 5, 2026', $box);
        $this->assertStringNotContainsString('Reviewed by', $box);
    }

    public function test_a_reviewer_alone_shows_the_name_but_no_date(): void
    {
        $this->makeArticle(['reviewer_name' => 'Sam Patel']);

        $box = $this->boxHtml($this->get('/articles/sleep-and-longevity')->getContent());

        $this->assertStringContainsString('Reviewed by', $box);
        $this->assertStringContainsString('Sam Patel', $box);
        $this->assertStringNotContainsString('Last reviewed', $box);
        $this->assertStringNotContainsString('Registered Dietitian', $box);
    }

    public function test_with_nothing_set_there_is_no_review_wording_anywhere_on_the_page(): void
    {
        $this->makeArticle();

        $response = $this->get('/articles/sleep-and-longevity');

        $response->assertOk();
        $html = $response->getContent();
        $this->assertStringContainsString('article-byline-box', $html, 'the author line is still there');
        $this->assertStringNotContainsString('Reviewed by', $html);
        $this->assertStringNotContainsString('Last reviewed', $html);
        $this->assertDoesNotMatchRegularExpression('/reviewed/i', $this->boxHtml($html));
        $this->assertDoesNotMatchRegularExpression('/medically/i', $html);
    }

    public function test_blank_or_whitespace_reviewer_fields_count_as_not_set(): void
    {
        $this->makeArticle(['reviewer_name' => '   ', 'reviewer_title' => '  ']);

        $html = $this->get('/articles/sleep-and-longevity')->getContent();

        $this->assertStringNotContainsString('Reviewed by', $html);
    }

    public function test_the_page_never_says_medically_reviewed_even_when_a_reviewer_is_set(): void
    {
        $this->makeArticle(['reviewer_name' => 'Sam Patel', 'reviewer_title' => 'MD', 'reviewed_at' => '2026-03-05']);

        $html = $this->get('/articles/sleep-and-longevity')->getContent();

        $this->assertDoesNotMatchRegularExpression('/medically/i', $html);
        $this->assertDoesNotMatchRegularExpression('/medical review/i', $html);
    }

    public function test_reviewer_text_is_escaped(): void
    {
        $this->makeArticle(['reviewer_name' => '<script>alert(1)</script>']);

        $html = $this->get('/articles/sleep-and-longevity')->getContent();

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
    }

    public function test_an_article_with_no_author_still_returns_200_and_has_no_author_line(): void
    {
        $this->makeArticle([], null);

        $response = $this->get('/articles/sleep-and-longevity');

        $response->assertOk();
        $html = $response->getContent();
        $response->assertSee('Body text of the article.', false);
        $this->assertStringNotContainsString('article-byline-box', $html);
        $this->assertStringNotContainsString('article-byline-author', $html);
    }

    public function test_an_article_with_no_author_but_a_reviewer_shows_only_the_reviewer_lines(): void
    {
        $this->makeArticle(['reviewer_name' => 'Sam Patel', 'reviewed_at' => '2026-03-05'], null);

        $response = $this->get('/articles/sleep-and-longevity');

        $response->assertOk();
        $box = $this->boxHtml($response->getContent());
        $this->assertStringContainsString('Reviewed by', $box);
        $this->assertStringNotContainsString('article-byline-author', $box);
    }

    public function test_the_box_is_small_muted_text_with_existing_bootstrap_classes_only(): void
    {
        $this->makeArticle();

        $box = $this->boxHtml($this->get('/articles/sleep-and-longevity')->getContent());

        $this->assertStringContainsString('class="article-byline-box small text-muted mb-3"', $box);
        $this->assertFileDoesNotExist(resource_path('css/article-byline-box.css'));
    }

    public function test_the_reviewer_migration_runs_down_and_up_on_the_test_database(): void
    {
        $this->assertTrue(Schema::hasColumns('articles', ['reviewer_name', 'reviewer_title', 'reviewed_at']));

        // A row that exists before the columns are removed or re-added must be left alone.
        $this->makeArticle(['content' => 'before']);

        $this->reviewerMigration()->down();
        $this->assertFalse(Schema::hasColumn('articles', 'reviewer_name'));
        $this->assertFalse(Schema::hasColumn('articles', 'reviewer_title'));
        $this->assertFalse(Schema::hasColumn('articles', 'reviewed_at'));
        $this->assertSame('before', DB::table('articles')->value('content'));
        $this->assertSame(1, DB::table('articles')->count());

        $this->reviewerMigration()->up();
        $this->assertTrue(Schema::hasColumns('articles', ['reviewer_name', 'reviewer_title', 'reviewed_at']));

        $row = DB::table('articles')->first();
        $this->assertNull($row->reviewer_name);
        $this->assertNull($row->reviewer_title);
        $this->assertNull($row->reviewed_at);
        $this->assertSame('before', $row->content);
    }
}
