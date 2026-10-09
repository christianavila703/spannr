<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * DEV-7. The three optional reviewer inputs in the admin article form: they are shown, validated and saved,
 * and left empty (null) when the editor leaves them empty.
 * The tables are created here (in-memory SQLite from phpunit.xml) so no real database is touched.
 */
class ArticleReviewerAdminTest extends TestCase
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
            $table->string('email')->nullable();
            $table->timestamps();
        });
        Schema::create('article_user', function (Blueprint $table) {
            $table->unsignedBigInteger('article_id');
            $table->unsignedBigInteger('user_id');
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
        Schema::create('record_locks', function (Blueprint $table) {
            $table->id();
            $table->morphs('lockable');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('created_at')->nullable();
        });

        // Spatie permission looks these up when the gate is checked, even though every check is allowed below.
        foreach (['permissions', 'roles'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
            });
        }
        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
        });
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
        });
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
        });

        DB::table('users')->insert(['id' => 1, 'name' => 'Editor', 'email' => 'editor@example.test', 'created_at' => now(), 'updated_at' => now()]);

        // Any signed-in user may edit articles in these tests. Permissions are not what is under test.
        Gate::before(fn () => true);
        $this->actingAs(User::find(1));
        $this->withoutVite();
    }

    private function makeArticle(array $attributes = []): Article
    {
        $now = now()->toDateTimeString();
        $id = DB::table('articles')->insertGetId(array_merge([
            'date' => $now, 'name' => 'Sleep and Longevity', 'slug' => 'sleep-and-longevity', 'status' => 'publish',
            'excerpt' => 'x', 'meta_description' => 'm', 'content' => '<p>Body</p>', 'created_at' => $now, 'updated_at' => $now,
        ], $attributes));

        return Article::findOrFail($id);
    }

    /** @return array<string, mixed> */
    private function formPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Sleep and Longevity',
            'slug' => 'sleep-and-longevity',
            'status' => 'publish',
            'date' => '2026-03-01T09:00',
            'excerpt' => 'x',
            'meta_description' => 'm',
            'content' => '<p>Body</p>',
            'authors' => [1],
        ], $overrides);
    }

    /**
     * The form partial is rendered on its own. The full admin page also loads the admin layout, whose search box
     * contacts Algolia, and a test run must make no network calls.
     */
    private function renderForm(?Article $article = null): string
    {
        $data = ['route' => '/admin/articles', 'authorsUrl' => '/json/authors', 'tagsUrl' => '/json/tags'];

        if ($article) {
            $data += ['article' => $article, 'method' => 'PUT'];
        }

        return view('admin.articles.form', $data)->render();
    }

    public function test_the_edit_form_shows_the_three_fields_empty_for_an_article_with_no_reviewer(): void
    {
        $html = $this->renderForm($this->makeArticle());

        $this->assertMatchesRegularExpression('/<input[^>]*type="text"[^>]*name="reviewer_name"[^>]*value=""/', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*type="text"[^>]*name="reviewer_title"[^>]*value=""/', $html);
        $this->assertMatchesRegularExpression('/<input[^>]*type="date"[^>]*name="reviewed_at"[^>]*value=""/', $html);
        $this->assertStringContainsString('Leave all three empty unless a named person has actually reviewed this article.', $html);
    }

    public function test_the_edit_form_shows_saved_values(): void
    {
        $article = $this->makeArticle(['reviewer_name' => 'Sam Patel', 'reviewer_title' => 'Registered Dietitian', 'reviewed_at' => '2026-03-05']);

        $html = $this->renderForm($article);

        $this->assertMatchesRegularExpression('/name="reviewer_name"[^>]*value="Sam Patel"/', $html);
        $this->assertMatchesRegularExpression('/name="reviewer_title"[^>]*value="Registered Dietitian"/', $html);
        $this->assertMatchesRegularExpression('/type="date"[^>]*name="reviewed_at"[^>]*value="2026-03-05"/', $html);
    }

    public function test_the_create_form_has_the_three_fields(): void
    {
        $html = $this->renderForm();

        $this->assertStringContainsString('name="reviewer_name"', $html);
        $this->assertStringContainsString('name="reviewer_title"', $html);
        $this->assertStringContainsString('name="reviewed_at"', $html);
    }

    public function test_saved_values_are_escaped_in_the_form(): void
    {
        $html = $this->renderForm($this->makeArticle(['reviewer_name' => '"><script>alert(1)</script>']));

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function test_updating_saves_all_three_fields(): void
    {
        $article = $this->makeArticle();

        $this->put('/admin/articles/' . $article->id, $this->formPayload([
            'reviewer_name' => 'Sam Patel',
            'reviewer_title' => 'Registered Dietitian',
            'reviewed_at' => '2026-03-05',
        ]))->assertSessionHasNoErrors();

        $row = DB::table('articles')->find($article->id);
        $this->assertSame('Sam Patel', $row->reviewer_name);
        $this->assertSame('Registered Dietitian', $row->reviewer_title);
        $this->assertSame('2026-03-05', substr($row->reviewed_at, 0, 10));
    }

    public function test_updating_with_empty_inputs_leaves_the_reviewer_fields_null(): void
    {
        $article = $this->makeArticle(['reviewer_name' => 'Sam Patel', 'reviewer_title' => 'RD', 'reviewed_at' => '2026-03-05']);

        $this->put('/admin/articles/' . $article->id, $this->formPayload([
            'reviewer_name' => '',
            'reviewer_title' => '',
            'reviewed_at' => '',
        ]))->assertSessionHasNoErrors();

        $row = DB::table('articles')->find($article->id);
        $this->assertNull($row->reviewer_name);
        $this->assertNull($row->reviewer_title);
        $this->assertNull($row->reviewed_at);
    }

    public function test_saving_without_the_fields_in_the_request_leaves_them_untouched(): void
    {
        $article = $this->makeArticle(['reviewer_name' => 'Sam Patel', 'reviewed_at' => '2026-03-05']);

        $this->put('/admin/articles/' . $article->id, $this->formPayload())->assertSessionHasNoErrors();

        $row = DB::table('articles')->find($article->id);
        $this->assertSame('Sam Patel', $row->reviewer_name);
        $this->assertSame('2026-03-05', substr($row->reviewed_at, 0, 10));
    }

    public function test_creating_an_article_saves_the_fields_and_defaults_to_null(): void
    {
        $this->post('/admin/articles', $this->formPayload([
            'name' => 'One', 'slug' => 'one', 'reviewer_name' => 'Sam Patel', 'reviewed_at' => '2026-03-05',
        ]))->assertSessionHasNoErrors();
        $this->post('/admin/articles', $this->formPayload(['name' => 'Two', 'slug' => 'two']))->assertSessionHasNoErrors();

        $one = DB::table('articles')->where('slug', 'one')->first();
        $this->assertSame('Sam Patel', $one->reviewer_name);
        $this->assertNull($one->reviewer_title);
        $this->assertSame('2026-03-05', substr($one->reviewed_at, 0, 10));

        $two = DB::table('articles')->where('slug', 'two')->first();
        $this->assertNull($two->reviewer_name);
        $this->assertNull($two->reviewer_title);
        $this->assertNull($two->reviewed_at);
    }

    public function test_bad_values_are_rejected(): void
    {
        $article = $this->makeArticle();

        $this->put('/admin/articles/' . $article->id, $this->formPayload([
            'reviewer_name' => str_repeat('a', 256),
            'reviewer_title' => ['not', 'a string'],
            'reviewed_at' => 'not a date',
        ]))->assertSessionHasErrors(['reviewer_name', 'reviewer_title', 'reviewed_at']);

        $this->assertNull(DB::table('articles')->find($article->id)->reviewer_name);
    }
}
