<?php

namespace Tests\Feature;

use App\Console\Commands\ExtractEmbeddedImages;
use App\Models\Article;
use App\Models\Content;
use App\Models\Glossary;
use App\Models\Job;
use App\Models\Listing;
use App\Models\Organization;
use App\Models\Traits\ExtractsEmbeddedImages;
use App\Support\EmbeddedImageExtractor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Concerns\MakesGdImages;
use Tests\TestCase;

/**
 * DEV-15. Saving a covered column with a pasted base64 image in it moves the image out into a file.
 * In-memory SQLite, faked storage and GD-made images: no real database or file is touched.
 */
class SaveTimeImageExtractionTest extends TestCase
{
    use MakesGdImages;

    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date')->nullable();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('status')->nullable();
            $table->mediumText('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->longText('sidebar')->nullable();
            $table->longText('sources')->nullable();
            $table->json('faq')->nullable();
            $table->date('content_updated_at')->nullable();
            $table->timestamps();
        });
        Schema::create('plain_pages', function (Blueprint $table) {
            $table->id();
            $table->longText('content')->nullable();
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

        Storage::fake('public');
        Storage::fake('local');
    }

    private function uri(int $seed = 1, int $width = 500, int $height = 300): array
    {
        $png = $this->pngBytes($this->noiseImage($width, $height, $seed), 1);

        return [$this->dataUri($png), $png];
    }

    private function publicFiles(): array
    {
        return Storage::disk('public')->allFiles();
    }

    public function test_saving_an_article_with_a_data_uri_stores_a_file_and_rewrites_the_text(): void
    {
        [$uri, $png] = $this->uri();

        $article = Article::create(['name' => 'Heavy', 'slug' => 'heavy', 'content' => '<p>Intro</p><p><img alt="A" src="' . $uri . '"></p>']);

        $path = 'images/articles/embedded/' . substr(sha1($png), 0, 12) . '.jpg';
        Storage::disk('public')->assertExists($path);
        $stored = DB::table('articles')->find($article->id)->content;
        $this->assertStringNotContainsString('data:image', $stored);
        $this->assertSame('<p>Intro</p><p><img alt="A" src="' . Storage::disk('public')->url($path) . '" width="500" height="300" loading="lazy"></p>', $stored);
        $this->assertSame($stored, $article->content, 'the model holds the converted text too');
    }

    public function test_updating_the_content_converts_it_in_every_covered_column(): void
    {
        [$uri] = $this->uri(2);
        $article = Article::create(['name' => 'Heavy', 'slug' => 'heavy', 'content' => '<p>Plain</p>']);

        $article->update(['sidebar' => '<img src="' . $uri . '">', 'sources' => '<img src="' . $uri . '">']);

        $row = DB::table('articles')->find($article->id);
        $this->assertStringNotContainsString('data:image', $row->sidebar);
        $this->assertStringNotContainsString('data:image', $row->sources);
        $this->assertSame('<p>Plain</p>', $row->content);
        $this->assertCount(1, $this->publicFiles(), 'one image used twice is one file');
    }

    public function test_html_around_the_image_is_kept_byte_for_byte(): void
    {
        [$uri, $png] = $this->uri(3, 200, 100);
        $before = "<h2 class=\"h3\">Title &amp; more</h2>\r\n<ul><li>One</li></ul>\n\t<p style=\"margin:0\">Text <strong>bold</strong></p>\n";
        $after = "\n<table><tr><td>cell</td></tr></table><!-- keep me --><img src=\"https://example.com/photo.jpg\" alt=\"external\">";

        $article = Article::create(['name' => 'Heavy', 'slug' => 'heavy', 'content' => $before . '<img alt="x" src="' . $uri . '">' . $after]);

        $path = 'images/articles/embedded/' . substr(sha1($png), 0, 12) . '.jpg';
        $expected = $before . '<img alt="x" src="' . Storage::disk('public')->url($path) . '" width="200" height="100" loading="lazy">' . $after;
        $this->assertSame($expected, DB::table('articles')->find($article->id)->content);
    }

    public function test_text_with_no_data_uri_is_saved_untouched_and_nothing_is_written(): void
    {
        $html = "<p>Plain &amp; simple</p>\r\n<img src=\"https://example.com/a.jpg\"><p>mentions data:image in words</p>";

        $article = Article::create(['name' => 'Light', 'slug' => 'light', 'content' => $html]);
        $article->update(['content' => $html . '<p>More</p>']);

        $this->assertSame($html . '<p>More</p>', DB::table('articles')->find($article->id)->content);
        $this->assertSame([], $this->publicFiles());
    }

    public function test_a_column_that_is_not_being_changed_is_not_looked_at(): void
    {
        [$uri] = $this->uri(4);
        $content = '<img src="' . $uri . '">';
        $id = DB::table('articles')->insertGetId(['name' => 'Old', 'slug' => 'old', 'content' => $content, 'created_at' => now(), 'updated_at' => now()]);

        // Only the name changes. The content still holds an old data URI, and this save must not touch it.
        Article::find($id)->update(['name' => 'Renamed']);

        $this->assertSame($content, DB::table('articles')->find($id)->content);
        $this->assertSame([], $this->publicFiles());
    }

    public function test_a_column_that_is_not_listed_is_never_converted(): void
    {
        [$uri] = $this->uri(5);
        $excerpt = '<img src="' . $uri . '">';

        $article = Article::create(['name' => 'Heavy', 'slug' => 'heavy', 'excerpt' => $excerpt, 'content' => '<p>x</p>']);

        $this->assertSame($excerpt, DB::table('articles')->find($article->id)->excerpt, 'excerpt is not in ExtractEmbeddedImages::COLUMNS');
        $this->assertSame([], $this->publicFiles());
    }

    public function test_a_failing_conversion_saves_the_original_text_and_logs_a_warning(): void
    {
        [$uri] = $this->uri(6);
        $content = '<p>Before</p><img src="' . $uri . '">';
        $this->app->bind(EmbeddedImageExtractor::class, fn () => new class extends EmbeddedImageExtractor {
            public function process(string $html): array
            {
                throw new \RuntimeException('disk is full');
            }
        });
        Log::shouldReceive('warning')->once()->withArgs(fn ($message) => str_contains($message, 'Article #(new) content')
            && str_contains($message, 'saved unchanged')
            && str_contains($message, 'disk is full'));

        $article = Article::create(['name' => 'Heavy', 'slug' => 'heavy', 'content' => $content]);

        $this->assertTrue($article->exists, 'the save went through');
        $this->assertSame($content, DB::table('articles')->find($article->id)->content, 'the text as it was typed');
    }

    public function test_an_image_that_cannot_be_decoded_is_left_in_place_and_the_save_still_works(): void
    {
        $content = '<img src="data:image/png;base64,' . base64_encode('not really an image') . '">';

        $article = Article::create(['name' => 'Odd', 'slug' => 'odd', 'content' => $content]);

        $this->assertSame($content, DB::table('articles')->find($article->id)->content);
        $this->assertSame([], $this->publicFiles());
    }

    public function test_the_save_sets_updated_at_exactly_as_a_normal_save_does(): void
    {
        [$uri] = $this->uri(7);
        $this->travelTo(now()->setDate(2026, 5, 5)->setTime(10, 0));
        $article = Article::create(['name' => 'Heavy', 'slug' => 'heavy', 'content' => '<p>x</p>']);

        $this->travelTo(now()->addDay());
        $article->update(['content' => '<img src="' . $uri . '">']);
        $withImage = DB::table('articles')->find($article->id)->updated_at;

        $this->travelTo(now()->addDay());
        $article->update(['content' => '<p>changed</p>']);
        $plain = DB::table('articles')->find($article->id)->updated_at;

        $this->assertSame('2026-05-06 10:00:00', $withImage, 'the usual timestamp, nothing extra');
        $this->assertSame('2026-05-07 10:00:00', $plain);
        $this->assertNull(DB::table('articles')->find($article->id)->content_updated_at, 'the hand-set date is never touched');
    }

    public function test_a_model_that_does_not_use_the_trait_or_whose_table_is_not_listed_is_unaffected(): void
    {
        [$uri] = $this->uri(8);
        $content = '<img src="' . $uri . '">';

        $withoutTrait = new class extends Model {
            protected $table = 'articles';
            protected $guarded = [];
        };
        $withoutTrait->fill(['name' => 'No trait', 'slug' => 'no-trait', 'content' => $content])->save();

        $unlistedTable = new class extends Model {
            use ExtractsEmbeddedImages;

            protected $table = 'plain_pages';
            protected $guarded = [];
        };
        $unlistedTable->fill(['content' => $content])->save();

        $this->assertSame($content, DB::table('articles')->where('slug', 'no-trait')->value('content'));
        $this->assertSame($content, DB::table('plain_pages')->value('content'));
        $this->assertSame([], $this->publicFiles());
    }

    public function test_every_table_the_command_covers_has_a_model_that_converts_on_save(): void
    {
        $models = [
            'articles' => Article::class,
            'glossary' => Glossary::class,
            'content' => Content::class,
            'listings' => Listing::class,
            'organizations' => Organization::class,
            'jobs' => Job::class,
        ];

        $this->assertEqualsCanonicalizing(array_keys(ExtractEmbeddedImages::COLUMNS), array_keys($models), 'a table was added to the command but not here');

        foreach ($models as $table => $class) {
            $this->assertSame($table, (new $class)->getTable(), $class);
            $this->assertContains(ExtractsEmbeddedImages::class, class_uses_recursive($class), $class . ' converts on save');
        }
    }
}
