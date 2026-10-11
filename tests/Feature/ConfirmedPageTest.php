<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * /confirmed is where subscribers land after confirming their email. GA4 counts a view of this URL
 * as a conversion, so the URL, the layout and the tags in the head must keep working.
 * The content table is created here (in-memory SQLite from phpunit.xml) so no real database is touched.
 */
class ConfirmedPageTest extends TestCase
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

    private function confirmedRecord(): void
    {
        // Shaped like the real record: the head tags are stored HTML, including a robots tag that says index.
        DB::table('content')->insert([
            'name' => 'Confirmed',
            'slug' => 'confirmed',
            'status' => 'Published',
            'seo' => '<title>Thank you for subscribing - Spannr</title>'
                . '<meta name="description" content="Stored description." />'
                . '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large" />',
            'content' => 'unused by this page',
            'meta_description' => 'Stored description.',
        ]);
    }

    public function test_the_page_renders_the_new_copy_in_order(): void
    {
        $this->confirmedRecord();

        $response = $this->get('/confirmed');

        $response->assertOk();
        $response->assertSeeInOrder([
            "You're in. Nice.",
            "Thanks for confirming. You're officially on the Spannr list, and I'm glad you're here.",
            'The Weekly Brief hits your inbox every Thursday at 7 am MST.',
            'Three quick things while you wait',
            'Explore Longevity Cities.',
            'Poke around the directory.',
            '900+ clinics, docs, coaches and retreats.',
            'Whitelist us.',
            'spannr@mail.beehiiv.com',
            "If something feels off or you've got a question, email me at",
            '<a href="mailto:brent@spannr.com">brent@spannr.com</a>',
            '. It comes straight to me.',
            'Brent',
        ], false);
        $response->assertSee('<h1 class="text-center mb-3">', false);
    }

    public function test_the_links_point_at_the_real_pages(): void
    {
        $this->confirmedRecord();

        $response = $this->get('/confirmed');

        $response->assertSee('href="' . url('/articles/longevity-doctors') . '"', false);
        $response->assertSee('href="' . url('/marketplace') . '"', false);
        $this->assertSame('/articles/longevity-doctors', route('public.content.articles.show', 'longevity-doctors', false));
        $this->assertSame('/marketplace', route('public.marketplace.index', [], false));
    }

    public function test_the_closing_line_has_a_mailto_link_to_brent(): void
    {
        $this->confirmedRecord();

        $html = $this->get('/confirmed')->getContent();

        $this->assertStringContainsString(
            "If something feels off or you've got a question, email me at <a href=\"mailto:brent@spannr.com\">brent@spannr.com</a>. It comes straight to me.",
            $html
        );
        $this->assertSame(1, substr_count($html, 'href="mailto:brent@spannr.com"'), 'exactly one mailto link, pointing at brent@spannr.com');
        $this->assertSame(0, preg_match('/href="mailto:(?!brent@spannr\.com")/', $html), 'no other mailto link on the page');
        $this->assertStringContainsString('Brent</p>', $html, 'the sign-off is kept');
    }

    public function test_the_words_hit_reply_appear_nowhere_on_the_page(): void
    {
        $this->confirmedRecord();

        $this->assertStringNotContainsStringIgnoringCase('hit reply', $this->get('/confirmed')->getContent());
    }

    public function test_the_words_hit_reply_are_also_gone_when_the_cms_record_is_missing(): void
    {
        $this->assertStringNotContainsStringIgnoringCase('hit reply', $this->get('/confirmed')->getContent());
    }

    public function test_the_old_mailchimp_era_copy_is_gone(): void
    {
        $this->confirmedRecord();

        $response = $this->get('/confirmed');

        $response->assertDontSee('Check your inbox for an email from admin@spannr.com', false);
        $response->assertDontSee('Looking to invest in the longevity ecosystem?', false);
    }

    public function test_the_copy_has_no_em_dash_or_en_dash(): void
    {
        $this->confirmedRecord();

        $html = $this->get('/confirmed')->getContent();
        $copy = substr($html, strpos($html, '<h1 class="text-center mb-3">'));
        $copy = substr($copy, 0, strpos($copy, 'Brent</p>') + strlen('Brent</p>'));

        $this->assertStringContainsString("You're in. Nice.", $copy);
        $this->assertSame(0, preg_match('/[\x{2012}-\x{2015}]/u', $copy));
    }

    public function test_noindex_follow_is_still_the_only_robots_tag_and_the_head_is_kept(): void
    {
        $this->confirmedRecord();

        $html = $this->get('/confirmed')->getContent();

        $this->assertSame(1, preg_match_all('/<meta name="robots"/', $html), 'exactly one robots tag');
        $this->assertStringContainsString('<meta name="robots" content="noindex, follow" />', $html);
        $this->assertStringNotContainsString('index, follow, max-snippet', $html, 'the stored index tag is stripped');
        $this->assertSame(1, substr_count($html, '<title>'), 'exactly one title');
        $this->assertStringContainsString('<title>Thank you for subscribing - Spannr</title>', $html);
    }

    public function test_the_analytics_tags_in_the_layout_are_untouched(): void
    {
        $this->confirmedRecord();

        $html = $this->get('/confirmed')->getContent();

        $this->assertStringContainsString("gtag('config', 'G-TW8JCMEBZF')", $html);
        $this->assertStringContainsString('https://www.googletagmanager.com/gtag/js?id=G-TW8JCMEBZF', $html);
        $this->assertStringContainsString('data-domain="spannr.com"', $html);
    }

    public function test_the_page_is_mobile_friendly_and_keeps_the_site_header_and_footer(): void
    {
        $this->confirmedRecord();

        $html = $this->get('/confirmed')->getContent();

        $this->assertStringContainsString('name="viewport" content="width=device-width, initial-scale=1"', $html);
        $this->assertStringContainsString('max-width-600', $html);
        $this->assertStringContainsString('text-break', $html, 'the long email address can wrap on a phone');
        $this->assertStringContainsString('class="public-main"', $html, 'rendered inside the public layout');
    }

    public function test_the_page_still_renders_if_the_cms_record_is_missing(): void
    {
        $this->get('/confirmed')->assertOk()->assertSee("You're in. Nice.", false);
    }

    /* ------------------------------------------------------------------ DEV-15: the head of /confirmed */

    private function storeSeo(string $seo): void
    {
        DB::table('content')->insert(['name' => 'Confirmed', 'slug' => 'confirmed', 'status' => 'Published', 'seo' => $seo]);
    }

    /** Asserts the head has exactly one title, one description and one robots tag (noindex), and no canonical. */
    private function assertCleanHead(string $html): void
    {
        $head = substr($html, 0, strpos($html, '</head>'));

        $this->assertSame(1, substr_count($head, '<title>'), 'one title');
        $this->assertSame(1, preg_match_all('/<meta name="description"/', $head), 'one description');
        $this->assertSame(1, preg_match_all('/<meta name="robots"/', $head), 'one robots tag');
        $this->assertStringContainsString('<meta name="robots" content="noindex, follow" />', $head);
        $this->assertStringNotContainsString('index, follow, max', $head, 'nothing index-able');
        $this->assertStringNotContainsString('canonical', $head, 'no canonical, least of all to another page');
        $this->assertStringContainsString("gtag('config', 'G-TW8JCMEBZF')", $head, 'GA4 is still in the head');
    }

    public function test_the_head_is_clean_when_the_cms_record_holds_everything_including_a_canonical_elsewhere(): void
    {
        $this->storeSeo('<title>Thank you for subscribing - Spannr</title>'
            . '<meta name="description" content="Stored description." />'
            . '<meta name="robots" content="index, follow, max-snippet:-1" />'
            . '<link rel="canonical" href="https://spannr.com/" />');

        $html = $this->get('/confirmed')->assertOk()->getContent();

        $this->assertCleanHead($html);
        $this->assertStringContainsString('<title>Thank you for subscribing - Spannr</title>', $html, 'the stored title is kept when there is one');
        $this->assertStringContainsString('content="Stored description."', $html);
    }

    public function test_a_title_and_description_are_added_when_the_cms_record_has_none(): void
    {
        $this->storeSeo('<meta property="og:locale" content="en_US" />');

        $html = $this->get('/confirmed')->assertOk()->getContent();

        $this->assertCleanHead($html);
        $this->assertStringContainsString('<title>Subscription confirmed - Spannr</title>', $html);
        $this->assertMatchesRegularExpression('/<meta name="description" content="[^"]{50,160}" \/>/', $html, 'a real sentence, not too long');
        $this->assertStringContainsString('og:locale', $html, 'the rest of the stored head is kept');
    }

    public function test_empty_stored_title_and_description_tags_do_not_count(): void
    {
        $this->storeSeo('<title></title><meta name="description" content="" />');

        $html = $this->get('/confirmed')->assertOk()->getContent();

        $this->assertCleanHead($html);
        $this->assertStringContainsString('<title>Subscription confirmed - Spannr</title>', $html);
    }

    public function test_the_head_is_clean_even_when_the_cms_record_is_missing(): void
    {
        $html = $this->get('/confirmed')->assertOk()->getContent();

        $this->assertCleanHead($html);
        $this->assertStringContainsString('<title>Subscription confirmed - Spannr</title>', $html);
    }

    public function test_the_page_text_and_the_analytics_scripts_are_unchanged(): void
    {
        $this->confirmedRecord();

        $html = $this->get('/confirmed')->getContent();

        $this->assertStringContainsString("You're in. Nice.", $html);
        $this->assertStringContainsString('https://www.googletagmanager.com/gtag/js?id=G-TW8JCMEBZF', $html);
        $this->assertStringContainsString('data-domain="spannr.com"', $html);
        $this->assertSame(1, substr_count($html, "gtag('config'"), 'the tracking config appears once');
    }
}
