<?php

namespace Tests\Feature;

use App\Http\Livewire\Articles\Index;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * DEV-15. /articles lists the newest content first: by the hand-set content_updated_at, or the published date when that
 * is empty. Never updated_at. Equal dates keep a stable order (highest id first).
 * In-memory SQLite: no real database is touched.
 */
class ArticlesIndexOrderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('status')->default('publish');
            $table->mediumText('excerpt')->nullable();
            $table->string('image')->nullable();
            $table->longText('content')->nullable();
            $table->date('content_updated_at')->nullable();
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

        // The index page looks up its CMS record (none here, so the page falls back to its defaults).
        Schema::create('content', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('status')->default('Published');
            $table->longText('seo')->nullable();
            $table->longText('content')->nullable();
            $table->timestamps();
        });

        $this->withoutVite();
    }

    private function article(string $slug, string $published, ?string $contentUpdated = null, string $updatedAt = '2026-01-01 00:00:00'): int
    {
        return DB::table('articles')->insertGetId([
            'name' => 'Title ' . $slug, 'slug' => $slug, 'date' => $published, 'content_updated_at' => $contentUpdated,
            'excerpt' => 'x', 'content' => '<p>x</p>', 'created_at' => $updatedAt, 'updated_at' => $updatedAt,
        ]);
    }

    /** @return array<int, string> the article slugs on the page, in the order they are listed */
    private function slugsOn(string $path = '/articles'): array
    {
        $html = $this->get($path)->assertOk()->getContent();
        preg_match_all('#<a href="' . preg_quote(url('/articles'), '#') . '/([^"]+)" class="post-title#', $html, $matches);

        return $matches[1];
    }

    public function test_an_article_with_a_newer_content_date_sorts_above_a_newer_published_one(): void
    {
        $this->article('published-later', '2026-03-01 09:00:00');
        $this->article('updated-later', '2026-01-01 09:00:00', '2026-06-01');

        $this->assertSame(['updated-later', 'published-later'], $this->slugsOn());
    }

    public function test_with_no_content_date_it_falls_back_to_the_published_date(): void
    {
        $this->article('oldest', '2026-01-01 09:00:00');
        $this->article('newest', '2026-05-01 09:00:00');
        $this->article('middle', '2026-03-01 09:00:00');

        $this->assertSame(['newest', 'middle', 'oldest'], $this->slugsOn());
    }

    public function test_an_old_content_date_on_a_recently_published_article_does_not_matter_more_than_the_dates_say(): void
    {
        $this->article('a', '2026-02-01 09:00:00', '2026-04-01');
        $this->article('b', '2026-05-01 09:00:00');
        $this->article('c', '2026-03-01 09:00:00');

        $this->assertSame(['b', 'a', 'c'], $this->slugsOn(), 'b (May 1), a (updated Apr 1), c (Mar 1)');
    }

    public function test_updated_at_is_never_used(): void
    {
        // "touched" was saved most recently (a typo fix), but its content is old.
        $this->article('touched', '2026-01-01 09:00:00', null, '2026-09-09 09:00:00');
        $this->article('real', '2026-02-01 09:00:00', null, '2026-02-01 09:00:00');

        $this->assertSame(['real', 'touched'], $this->slugsOn());
    }

    public function test_equal_dates_keep_a_stable_order_with_the_highest_id_first(): void
    {
        foreach (['one', 'two', 'three'] as $slug) {
            $this->article($slug, '2026-04-01 09:00:00');
        }
        $this->article('four', '2026-01-01 09:00:00', '2026-04-01');

        $first = $this->slugsOn();

        // "three", "two" and "one" tie on the id; "four" has only a date (midnight), which is earlier than 09:00 that day.
        $this->assertSame(['three', 'two', 'one', 'four'], $first);
        $this->assertSame($first, $this->slugsOn(), 'the same on every request');
    }

    public function test_a_tie_does_not_reshuffle_across_pages(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $this->article('same-' . str_pad((string) $i, 2, '0', STR_PAD_LEFT), '2026-04-01 09:00:00');
        }

        $pageOne = $this->slugsOn('/articles');
        $pageTwo = $this->slugsOn('/articles?page=2');

        $this->assertSame(['same-12', 'same-11', 'same-10', 'same-09', 'same-08', 'same-07', 'same-06', 'same-05', 'same-04', 'same-03'], $pageOne);
        $this->assertSame(['same-02', 'same-01'], $pageTwo, 'nothing repeated or lost between pages');
    }

    public function test_the_search_filter_still_works_and_keeps_the_order(): void
    {
        $this->article('zebra-old', '2026-01-01 09:00:00');
        $this->article('zebra-new', '2026-05-01 09:00:00');
        DB::table('articles')->insert([
            'name' => 'Something else', 'slug' => 'other', 'date' => '2026-06-01 09:00:00', 'excerpt' => 'x', 'content' => '<p>x</p>',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        Livewire::test(Index::class)
            ->set('filters.search', 'zebra')
            ->assertSeeInOrder(['Title zebra-new', 'Title zebra-old'])
            ->assertDontSee('Something else');
    }
}
