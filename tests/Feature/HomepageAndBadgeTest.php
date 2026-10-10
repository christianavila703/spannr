<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Article;
use App\Models\Listing;
use DOMDocument;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;
use Tests\Feature\Concerns\BuildsPublicPageTables;
use Tests\TestCase;

/**
 * DEV-10.
 *  - The homepage lists its items in the first HTML response (no JavaScript needed to see titles and links).
 *  - The "Add the Spannr badge to your site" section on clinic and doctor profiles, and the badge SVG itself.
 * The tables are created here (in-memory SQLite from phpunit.xml) so no real database is touched.
 */
class HomepageAndBadgeTest extends TestCase
{
    use BuildsPublicPageTables;

    private const BADGE_PATH = '/images/badges/find-us-on-spannr.svg';

    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        $this->createTables();

        // The layout asks Vite for built assets, which do not exist in a test run.
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

    /* ------------------------------------------------------------------ helpers */

    private function makeListing(string $name, string $type = Listing::TYPE_CLINIC, string $status = Listing::STATUS_PUBLIC): Listing
    {
        $listing = Listing::factory()->create(['name' => $name, 'type' => $type, 'status' => $status]);
        Address::factory()->for($listing, 'addressable')->create(['name' => '100 Main Street, Miami, FL', 'street' => '100 Main Street']);

        return $listing->fresh();
    }

    private function page(Listing $listing): string
    {
        return $this->get('/marketplace/' . strtolower($listing->type) . '/' . $listing->slug)->assertOk()->getContent();
    }

    /** The badge section's HTML, or '' when the page has none. */
    private function section(string $html): string
    {
        $start = strpos($html, '<section class="listing-badge');

        return $start === false ? '' : substr($html, $start, strpos($html, '</section>', $start) - $start + 10);
    }

    /** The text of the snippet box, decoded the way a browser shows it. */
    private function snippet(string $section): string
    {
        preg_match('#<textarea[^>]*id="listing-badge-snippet"[^>]*>(.*?)</textarea>#s', $section, $match);

        return html_entity_decode($match[1] ?? '', ENT_QUOTES | ENT_HTML5);
    }

    /* ------------------------------------------------------------------ the badge section */

    public function test_clinic_and_doctor_profiles_have_the_badge_section(): void
    {
        foreach ([Listing::TYPE_CLINIC, Listing::TYPE_DOCTOR] as $type) {
            $section = $this->section($this->page($this->makeListing('Some ' . $type, $type)));

            $this->assertNotSame('', $section, $type);
            $this->assertStringContainsString('<h2 class="h4 mb-3" id="listing-badge-title">Add the Spannr badge to your site</h2>', $section, $type);
            $this->assertStringContainsString('Find us on Spannr? Show it on your website and link your visitors back to your profile.', $section, $type);
            $this->assertStringContainsString('<img src="' . url(self::BADGE_PATH) . '" width="200" height="60" alt="Find us on Spannr">', $section, $type . ': the preview');
            $this->assertMatchesRegularExpression('/<textarea[^>]*\breadonly\b/', $section, $type);
            $this->assertMatchesRegularExpression('#<button type="button"[^>]*id="listing-badge-copy"[^>]*>Copy</button>#', $section, $type);
        }
    }

    public function test_coach_and_retreat_profiles_do_not_have_it(): void
    {
        foreach ([Listing::TYPE_COACH, Listing::TYPE_RETREAT] as $type) {
            $html = $this->page($this->makeListing('Some ' . $type, $type));

            $this->assertSame('', $this->section($html), $type);
            $this->assertStringNotContainsString('Add the Spannr badge', $html, $type);
        }
    }

    public function test_a_pending_profile_has_no_badge_section_because_visitors_cannot_open_it(): void
    {
        $pending = $this->makeListing('Pending Clinic', Listing::TYPE_CLINIC, Listing::STATUS_PENDING);

        $this->assertSame('', trim(view('public.marketplace._badge', ['listing' => $pending])->render()));
    }

    public function test_the_snippet_links_to_this_listings_profile_around_the_absolute_badge_image(): void
    {
        $this->makeListing('Other Clinic');
        $clinic = $this->makeListing('Alpha Longevity Clinic');

        $snippet = $this->snippet($this->section($this->page($clinic)));

        $this->assertSame(
            '<a href="' . url('/marketplace/clinic/alpha-longevity-clinic') . '"><img src="' . url(self::BADGE_PATH) . '" width="200" height="60" alt="Find us on Spannr"></a>',
            $snippet,
        );
        $this->assertStringStartsWith('<a href="http', $snippet, 'absolute URLs, so it works on another site');
        $this->assertStringNotContainsString('other-clinic', $snippet);
        $this->assertStringNotContainsString('rel=', $snippet);
        $this->assertStringNotContainsStringIgnoringCase('nofollow', $snippet);
    }

    public function test_the_doctor_snippet_uses_the_doctor_profile_url(): void
    {
        $doctor = $this->makeListing('Dr Jane Doe', Listing::TYPE_DOCTOR);

        $this->assertStringContainsString(
            '<a href="' . url('/marketplace/doctor/dr-jane-doe') . '"><img src="' . url(self::BADGE_PATH) . '"',
            $this->snippet($this->section($this->page($doctor))),
        );
    }

    public function test_a_listing_name_with_markup_cannot_break_the_snippet_or_the_page(): void
    {
        $clinic = $this->makeListing('Evil </textarea><script>alert(1)</script> Clinic');

        $html = $this->page($clinic);

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertSame(1, substr_count($this->section($html), '</textarea>'));
        $this->assertStringStartsWith('<a href="' . url('/marketplace/clinic/'), $this->snippet($this->section($html)));
    }

    public function test_the_section_sits_directly_under_the_claim_box_and_above_the_city_links(): void
    {
        $html = $this->page($this->makeListing('Alpha Longevity Clinic'));

        $claim = strpos($html, 'Is this your Clinic?');
        $badge = strpos($html, '<section class="listing-badge');
        $cityLinks = strpos($html, 'class="listing-city-links');

        $this->assertNotFalse($claim, 'the claim box shows for a listing nobody has claimed');
        $this->assertGreaterThan($claim, $badge);
        $this->assertLessThan($cityLinks, $badge);
    }

    public function test_the_section_has_the_copy_script_with_a_select_fallback_and_no_new_dependency(): void
    {
        $section = $this->section($this->page($this->makeListing('Alpha Longevity Clinic')));

        $this->assertStringContainsString('navigator.clipboard.writeText', $section);
        $this->assertStringContainsString("document.execCommand('copy')", $section);
        $this->assertStringContainsString('box.select()', $section);
        $this->assertSame(1, substr_count($section, '<script'), 'one inline script');
        $this->assertStringNotContainsString('<script src', $section, 'no external script');
        $this->assertStringNotContainsString('wire:', $section, 'plain HTML, not a Livewire component');
    }

    public function test_the_wording_is_neutral(): void
    {
        foreach ([Listing::TYPE_CLINIC, Listing::TYPE_DOCTOR] as $type) {
            $section = $this->section($this->page($this->makeListing('Some ' . $type, $type)));
            $visible = strip_tags($section);

            $this->assertStringNotContainsStringIgnoringCase('featured', $section, $type);
            $this->assertDoesNotMatchRegularExpression('/\b(top|best|ranked|recommended|endorsed|sponsor\w*|promoted)\b/i', $visible, $type);
            $this->assertSame(0, preg_match('/[\x{2012}-\x{2015}]/u', $section), $type . ': no dashes');
        }
    }

    /* ------------------------------------------------------------------ the badge file */

    public function test_the_badge_file_exists_is_valid_xml_and_has_a_title(): void
    {
        $path = public_path(self::BADGE_PATH);
        $this->assertFileExists($path);

        $doc = new DOMDocument();
        $this->assertTrue($doc->load($path), 'well-formed XML');
        $this->assertSame('svg', $doc->documentElement->localName);
        $this->assertSame('http://www.w3.org/2000/svg', $doc->documentElement->namespaceURI);

        $titles = $doc->getElementsByTagName('title');
        $this->assertSame(1, $titles->length);
        $this->assertSame('Find us on Spannr', $titles->item(0)->textContent);
        $this->assertSame('img', $doc->documentElement->getAttribute('role'));
        $this->assertSame($titles->item(0)->getAttribute('id'), $doc->documentElement->getAttribute('aria-labelledby'));
    }

    /** @return array<int, \DOMElement> */
    private function badgeTextLines(DOMDocument $doc): array
    {
        $lines = [];
        foreach ($doc->getElementsByTagName('text') as $text) {
            $lines[] = $text;
        }

        return $lines;
    }

    /** The text-anchor that applies to an element: its own attribute, else the nearest ancestor's. */
    private function effectiveAnchor(\DOMElement $element): ?string
    {
        for ($node = $element; $node instanceof \DOMElement; $node = $node->parentNode) {
            if ($node->hasAttribute('text-anchor')) {
                return $node->getAttribute('text-anchor');
            }
        }

        return null;
    }

    private function badgeDocument(): DOMDocument
    {
        $doc = new DOMDocument();
        $this->assertTrue($doc->load(public_path(self::BADGE_PATH)));

        return $doc;
    }

    public function test_the_badge_text_reads_find_us_on_and_spannr_in_capitals(): void
    {
        $lines = $this->badgeTextLines($this->badgeDocument());

        $this->assertSame(['FIND US ON', 'SPANNR'], array_map(fn ($line) => $line->textContent, $lines));
    }

    public function test_the_badge_is_a_pink_background_with_a_white_logo_and_white_text(): void
    {
        $doc = $this->badgeDocument();
        $svg = file_get_contents(public_path(self::BADGE_PATH));

        $rects = $doc->getElementsByTagName('rect');
        $this->assertSame(1, $rects->length);
        $this->assertSame('#ed008c', $rects->item(0)->getAttribute('fill'), 'the pink is the background');
        $this->assertSame('400', $rects->item(0)->getAttribute('width'));
        $this->assertSame('120', $rects->item(0)->getAttribute('height'), 'the background covers the whole badge');

        // Every group that draws something (the logo, the text) is filled white, and no other fill is used.
        $fills = [];
        foreach ($doc->getElementsByTagName('g') as $group) {
            $fills[] = $group->getAttribute('fill');
        }
        $this->assertSame(['#ffffff', '#ffffff'], $fills, 'logo group and text group');
        $this->assertSame(1, $doc->getElementsByTagName('title')->length);
        preg_match_all('/fill="([^"]*)"/', $svg, $all);
        $this->assertEqualsCanonicalizing(['#ed008c', '#ffffff', '#ffffff'], $all[1], 'no other fill anywhere');
        $this->assertSame(3, $doc->getElementsByTagName('path')->length, 'the logo mark, drawn in the white group');
    }

    public function test_the_badge_has_no_blue_and_no_outline(): void
    {
        $svg = file_get_contents(public_path(self::BADGE_PATH));

        $this->assertStringNotContainsStringIgnoringCase('0ac7fe', $svg, 'no blue');
        $this->assertStringNotContainsStringIgnoringCase('0ac6ff', $svg, 'no blue');
        $this->assertStringNotContainsString('stroke', $svg, 'no outline');
    }

    public function test_the_two_text_lines_are_centered_on_each_other(): void
    {
        $lines = $this->badgeTextLines($this->badgeDocument());

        $this->assertCount(2, $lines);
        foreach ($lines as $line) {
            $this->assertSame('middle', $this->effectiveAnchor($line), $line->textContent);
        }
        $this->assertSame($lines[0]->getAttribute('x'), $lines[1]->getAttribute('x'), 'the same centre line');
        $this->assertNotSame('', $lines[0]->getAttribute('x'));
    }

    public function test_the_badge_uses_no_external_resources_and_no_web_fonts(): void
    {
        $svg = file_get_contents(public_path(self::BADGE_PATH));

        $this->assertDoesNotMatchRegularExpression('#(https?:)?//(?!www\.w3\.org)#', $svg, 'no external URL');
        $this->assertSame(1, substr_count($svg, 'http'), 'the only http text is the xmlns');
        $this->assertStringNotContainsString('@import', $svg);
        $this->assertStringNotContainsString('@font-face', $svg);
        $this->assertStringNotContainsString('<image', $svg);
        $this->assertStringNotContainsString('<style', $svg);
        $this->assertStringNotContainsStringIgnoringCase('featured', $svg);
    }

    public function test_the_badge_is_wider_than_tall_so_it_reads_at_two_hundred_pixels(): void
    {
        $doc = new DOMDocument();
        $doc->load(public_path(self::BADGE_PATH));
        [, , $width, $height] = array_map('floatval', explode(' ', $doc->documentElement->getAttribute('viewBox')));

        $this->assertSame(200 * $height / $width, 60.0, 'the 200 by 60 the snippet asks for');
    }

    /* ------------------------------------------------------------------ the homepage, in the first response */

    private function homepage(): string
    {
        DB::table('content')->insert([
            'name' => 'home', 'slug' => 'home', 'status' => 'Published', 'seo' => '<meta name="description" content="Spannr home" />',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return $this->get('/')->assertOk()->getContent();
    }

    public function test_the_homepage_item_titles_and_links_are_in_the_first_html_response(): void
    {
        foreach (['Sleep and Longevity', 'Cold Exposure Basics', 'NAD Boosters Explained'] as $i => $name) {
            Article::factory()->create(['name' => $name, 'slug' => str($name)->slug(), 'date' => now()->subDays($i)]);
        }

        $html = $this->homepage();

        // "Most Recent" and "Trending Insights" are Blade loops: every title and its link is in the markup a crawler fetches.
        foreach (['sleep-and-longevity' => 'Sleep and Longevity', 'cold-exposure-basics' => 'Cold Exposure Basics', 'nad-boosters-explained' => 'NAD Boosters Explained'] as $slug => $name) {
            $this->assertStringContainsString('<a href="' . url('/articles/' . $slug) . '">', $html, $slug);
            $this->assertStringContainsString('<h3 class="post-title">' . $name . '</h3>', $html, $slug);
        }

        // "Popular Longevity Services": the five service links are plain anchors.
        $this->assertStringContainsString('Popular Longevity Services', $html);
        foreach (['cold-plunge', 'red-light-therapy', 'sauna', 'cryotherapy', 'iv-therapy'] as $service) {
            $this->assertStringContainsString('<a href="' . url('/marketplace/' . $service) . '">', $html, $service);
        }
    }

    public function test_the_two_sections_load_nothing_with_javascript_or_on_a_delay(): void
    {
        $sources = '';
        foreach (['public/content/home/index', 'public/marketplace/_nav', 'public/marketplace/_hero'] as $view) {
            $sources .= file_get_contents(resource_path('views/' . $view . '.blade.php'));
        }

        $this->assertStringNotContainsString('wire:init', $sources);
        $this->assertStringNotContainsString('lazy', preg_replace('/loading="lazy"/', '', $sources), 'no lazy Livewire component (only lazy-loaded images)');
        $this->assertStringNotContainsString('fetch(', $sources);
        $this->assertStringNotContainsString('x-init', $sources);
        $this->assertStringNotContainsString('x-data', $sources);
    }
}
