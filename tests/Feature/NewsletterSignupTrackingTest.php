<?php

namespace Tests\Feature;

use App\Http\Livewire\Forms\NewsletterOptIn;
use App\Models\FormEntry;
use App\Support\NewsletterSignupContext;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\Feature\Concerns\BuildsPublicPageTables;
use Tests\TestCase;

/**
 * DEV-6. Newsletter signup tracking: every signup block carries a position label, a successful signup keeps where it
 * happened, and /confirmed fires one GA4 "newsletter_signup" event with it.
 *
 * No real service is called: Http::fake covers beehiiv, and the tables are created here (in-memory SQLite).
 */
class NewsletterSignupTrackingTest extends TestCase
{
    use BuildsPublicPageTables;

    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        $this->createTables();
        Schema::create('form_entries', function (Blueprint $table) {
            $table->id();
            $table->string('form');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email');
            $table->json('fields');
            $table->foreignId('user_id')->nullable();
            $table->string('url')->nullable();
            $table->string('mailchimp_status')->nullable();
            $table->json('mailchimp_response')->nullable();
            $table->nullableMorphs('entity');
            $table->timestamps();
        });

        Http::fake();
        $this->withoutVite();
    }

    /* ------------------------------------------------------------------ helpers */

    /**
     * The position of every newsletter signup block in the page, in page order, read from the Livewire data the page
     * ships with each component.
     *
     * @return array<int, string>
     */
    private function positionsIn(string $html): array
    {
        preg_match_all('/wire:initial-data="([^"]*)"/', $html, $matches);

        $positions = [];

        foreach ($matches[1] as $encoded) {
            $data = json_decode(html_entity_decode($encoded, ENT_QUOTES | ENT_HTML5), true);

            if (($data['fingerprint']['name'] ?? null) === 'forms.newsletter-opt-in') {
                $positions[] = $data['serverMemo']['data']['position'];
            }
        }

        return $positions;
    }

    private function article(string $slug, string $name = 'Sleep and Longevity'): void
    {
        DB::table('articles')->insert([
            'date' => '2026-01-10 09:30:00', 'name' => $name, 'slug' => $slug, 'status' => 'publish', 'excerpt' => 'x',
            'meta_description' => 'm', 'content' => '<p>Body</p>', 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function glossary(): void
    {
        foreach ([['NAD', 'nad'], ['Sirtuin', 'sirtuin']] as [$name, $slug]) {
            DB::table('glossary')->insert([
                'name' => $name, 'slug' => $slug, 'group' => substr($name, 0, 1), 'status' => 'publish', 'excerpt' => 'x', 'content' => 'x',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function signUp(string $position, ?string $url = null): \Livewire\Testing\TestableLivewire
    {
        $component = Livewire::test(NewsletterOptIn::class)->set('position', $position);

        if ($url !== null) {
            $component->set('url', $url);
        }

        return $component->set('email', 'visitor@example.com')->call('submit');
    }

    /** A request to /confirmed that carries the given session values and the session cookie of an earlier request. */
    private function confirmed(array $session = [], ?string $sessionId = null): array
    {
        // The cookie is read and sent back as it is, so the same session is used for a second visit.
        $this->withoutMiddleware(\Illuminate\Cookie\Middleware\EncryptCookies::class);

        $request = $sessionId ? $this->withCookie(config('session.cookie'), $sessionId) : $this->withSession($session);
        $response = $request->get('/confirmed')->assertOk();

        $cookie = collect($response->headers->getCookies())->first(fn ($cookie) => $cookie->getName() === config('session.cookie'));

        return [$response->getContent(), $cookie?->getValue()];
    }

    private function eventsIn(string $html): int
    {
        return substr_count($html, "'newsletter_confirmed'");
    }

    /* ------------------------------------------------------------------ A: every placement carries its label */

    public function test_the_homepage_has_the_header_modal_the_menu_and_the_homepage_block(): void
    {
        DB::table('content')->insert(['name' => 'home', 'slug' => 'home', 'status' => 'Published', 'seo' => '<meta name="description" content="x" />', 'created_at' => now(), 'updated_at' => now()]);

        $positions = $this->positionsIn($this->get('/')->assertOk()->getContent());

        $this->assertEqualsCanonicalizing(['header_modal', 'menu', 'homepage'], $positions, 'header modal, hamburger menu, homepage bottom box');
    }

    public function test_an_article_page_labels_its_box_mid_article(): void
    {
        $this->article('sleep-and-longevity');

        $positions = $this->positionsIn($this->get('/articles/sleep-and-longevity')->assertOk()->getContent());

        $this->assertEqualsCanonicalizing(['header_modal', 'menu', 'mid_article'], $positions);
    }

    public function test_city_guides_and_the_national_hub_label_their_box_city_guide(): void
    {
        foreach (['longevity-doctors-miami-fl', 'longevity-doctors-austin', 'longevity-doctors'] as $slug) {
            $this->article($slug, 'Guide ' . $slug);

            $positions = $this->positionsIn($this->get('/articles/' . $slug)->assertOk()->getContent());

            $this->assertEqualsCanonicalizing(['header_modal', 'menu', 'city_guide'], $positions, $slug);
        }
    }

    public function test_an_article_whose_slug_only_looks_like_a_guide_is_not_one(): void
    {
        $this->article('best-longevity-doctors');

        // "best-longevity-doctors" does not start with the guide prefix, so it is an ordinary article.
        $this->assertContains('mid_article', $this->positionsIn($this->get('/articles/best-longevity-doctors')->getContent()));
        $this->assertNotContains('city_guide', $this->positionsIn($this->get('/articles/best-longevity-doctors')->getContent()));
    }

    public function test_glossary_pages_label_their_box_glossary(): void
    {
        $this->glossary();

        $this->assertEqualsCanonicalizing(['header_modal', 'menu', 'glossary'], $this->positionsIn($this->get('/glossary/nad')->assertOk()->getContent()));
        $this->assertEqualsCanonicalizing(['header_modal', 'menu', 'glossary'], $this->positionsIn($this->get('/glossary')->assertOk()->getContent()));
    }

    public function test_every_signup_block_in_the_views_says_where_it_is(): void
    {
        // The pages that are hard to render here (company, investor and marketplace pages, the about page) are checked at source.
        $expected = [
            'public/includes/header.blade.php' => ['header_modal'],
            'public/includes/menu-primary.blade.php' => ['menu'],
            'public/content/about/index.blade.php' => ['about'],
            'public/content/home/index.blade.php' => ['homepage'],
            'public/content/glossary/show.blade.php' => ['glossary'],
            'public/content/glossary/index.blade.php' => ['glossary'],
            'public/content/organizations/show.blade.php' => ['other'],
            'public/content/investors/index.blade.php' => ['investors'],
            'public/content/articles/show.blade.php' => ['{{ $newsletterPosition }}'],
            'public/marketplace/content/sauna/index.blade.php' => ['other'],
            'public/marketplace/content/cryotherapy/index.blade.php' => ['other'],
            'public/marketplace/content/iv-therapy/index.blade.php' => ['other'],
            'public/marketplace/content/red-light-therapy/index.blade.php' => ['other'],
            'public/marketplace/content/cold-plunge/index.blade.php' => ['other'],
        ];

        foreach ($expected as $file => $positions) {
            $source = file_get_contents(resource_path('views/' . $file));
            preg_match_all('/<(?:livewire:forms\.newsletter-opt-in|x-optins\.newsletter)\b[^>]*?>/s', $source, $tags);

            $this->assertCount(count($positions), $tags[0], $file . ': one signup block per expected position');

            foreach ($tags[0] as $i => $tag) {
                $this->assertMatchesRegularExpression('/\bposition="' . preg_quote($positions[$i], '/') . '"/', $tag, $file);
            }
        }

        // And no view anywhere renders a signup block without a position.
        $unlabelled = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views'))) as $file) {
            if (! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }

            preg_match_all('/<(?:livewire:forms\.newsletter-opt-in|x-optins\.newsletter)\b[^>]*?>/s', file_get_contents($file->getPathname()), $tags);

            foreach ($tags[0] as $tag) {
                if (! str_contains($tag, 'position=')) {
                    $unlabelled[] = $file->getPathname();
                }
            }
        }
        $this->assertSame([], $unlabelled, 'every newsletter block has a position');
    }

    public function test_every_position_used_in_a_view_is_in_the_list(): void
    {
        $used = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views'))) as $file) {
            if (str_ends_with($file->getFilename(), '.blade.php') && preg_match_all('/<(?:livewire:forms\.newsletter-opt-in|x-optins\.newsletter)\b[^>]*?\bposition="([a-z_]+)"/s', file_get_contents($file->getPathname()), $found)) {
                array_push($used, ...$found[1]);
            }
        }

        $this->assertNotEmpty($used);
        $this->assertSame([], array_diff($used, NewsletterSignupContext::POSITIONS));
        $this->assertSame(
            ['header_modal', 'footer', 'homepage', 'mid_article', 'end_of_article', 'listing_page', 'city_guide', 'glossary', 'menu', 'about', 'investors', 'other'],
            NewsletterSignupContext::POSITIONS,
            'the list the ticket defines, in one place',
        );
    }

    /* ------------------------------------------------------------------ A: the whitelist */

    public function test_an_unknown_position_is_stored_as_other(): void
    {
        foreach (['', 'unknown', 'banner', 'HEADER_MODAL', '</script>', 'homepage ', "mid_article\n"] as $bad) {
            $this->assertSame('other', NewsletterSignupContext::normalizePosition($bad), var_export($bad, true));
        }
        $this->assertSame('other', NewsletterSignupContext::normalizePosition(null));
        $this->assertSame('other', NewsletterSignupContext::normalizePosition(['homepage']));

        foreach (NewsletterSignupContext::POSITIONS as $good) {
            $this->assertSame($good, NewsletterSignupContext::normalizePosition($good));
        }
    }

    public function test_the_component_defaults_to_unknown_and_stores_other_for_a_bad_position(): void
    {
        $this->assertSame('unknown', Livewire::test(NewsletterOptIn::class)->get('position'));

        foreach (['unknown', 'not-a-position', '"><script>alert(1)</script>'] as $bad) {
            DB::table('form_entries')->delete();
            $this->signUp($bad);

            $this->assertSame('other', json_decode(FormEntry::first()->fields, true)['position'], $bad);
            $this->assertSame('other', session(NewsletterSignupContext::SESSION_KEY)['block_position'], $bad);
        }
    }

    public function test_a_real_position_is_stored_with_the_signup_and_kept_for_confirmed(): void
    {
        $this->signUp('homepage');

        $this->assertSame('homepage', json_decode(FormEntry::first()->fields, true)['position']);
        $this->assertSame('homepage', session(NewsletterSignupContext::SESSION_KEY)['block_position']);
    }

    /* ------------------------------------------------------------------ B: the context kept at signup */

    public function test_a_successful_signup_keeps_the_page_url_and_the_position_in_the_session(): void
    {
        $component = $this->signUp('glossary', 'http://localhost/glossary/nad?utm_source=x');

        $component->assertSee('Check your email for the confirm link');
        $this->assertSame(
            ['block_position' => 'glossary', 'page_location' => 'http://localhost/glossary/nad?utm_source=x'],
            session(NewsletterSignupContext::SESSION_KEY),
        );
        $this->assertSame(1, FormEntry::count(), 'the signup itself still works as before');
    }

    public function test_the_page_url_has_no_fragment_and_is_capped_at_500_characters(): void
    {
        $this->signUp('footer', 'http://localhost/articles/x?q=' . str_repeat('a', 800) . '#comments');

        $location = session(NewsletterSignupContext::SESSION_KEY)['page_location'];
        $this->assertSame(500, strlen($location));
        $this->assertStringStartsWith('http://localhost/articles/x?q=aaa', $location);
        $this->assertStringNotContainsString('#', $location);

        $this->assertSame('http://localhost/a?b=1', NewsletterSignupContext::cleanUrl('http://localhost/a?b=1#one#two', 'localhost'), 'everything after the first # goes');
        $this->assertSame(500, mb_strlen(NewsletterSignupContext::cleanUrl('http://localhost/' . str_repeat('é', 900), 'localhost')), 'counted in characters');
    }

    public function test_a_page_url_that_is_not_on_this_site_or_not_http_is_dropped(): void
    {
        foreach (['https://evil.example/phish', 'javascript:alert(1)', 'http://localhost.evil.example/', '//localhost/x', 'http://localhost/a b', "http://localhost/a\nb", '', null] as $bad) {
            $this->assertNull(NewsletterSignupContext::cleanUrl($bad, 'localhost'), var_export($bad, true));
        }

        $this->signUp('footer', 'https://evil.example/phish');
        $this->assertNull(session(NewsletterSignupContext::SESSION_KEY)['page_location'], 'a tampered URL is not kept');
    }

    public function test_the_referer_is_used_first_and_the_component_url_is_the_fallback(): void
    {
        $this->assertSame('http://localhost/from-referer', NewsletterSignupContext::pageUrl('http://localhost/from-referer#x', 'http://localhost/from-component', 'localhost'));
        $this->assertSame('http://localhost/from-component', NewsletterSignupContext::pageUrl(null, 'http://localhost/from-component', 'localhost'));
        $this->assertSame('http://localhost/from-component', NewsletterSignupContext::pageUrl('https://evil.example/', 'http://localhost/from-component', 'localhost'));
        $this->assertNull(NewsletterSignupContext::pageUrl('https://evil.example/', 'https://evil.example/', 'localhost'));
    }

    public function test_a_tracking_problem_never_breaks_the_signup(): void
    {
        // A session that cannot be written to.
        $this->app->instance('session.store', new class($this->app['session.store']->getName(), new \Illuminate\Session\ArraySessionHandler(120)) extends \Illuminate\Session\Store {
            public function put($key, $value = null)
            {
                throw new \RuntimeException('session is broken');
            }
        });

        $component = Livewire::test(NewsletterOptIn::class)->set('email', 'visitor@example.com')->call('submit');

        $component->assertSee('Check your email for the confirm link');
        $this->assertSame(1, FormEntry::count(), 'the signup was saved');
    }

    /* ------------------------------------------------------------------ the submit event (the browser event the page script turns into GA4) */

    public function test_a_valid_submit_dispatches_the_browser_event_with_the_position(): void
    {
        foreach (['footer', 'menu', 'investors', 'about'] as $position) {
            Livewire::test(NewsletterOptIn::class)
                ->set('position', $position)
                ->set('email', 'visitor@example.com')
                ->call('submit')
                ->assertDispatchedBrowserEvent('newsletter-signup', ['position' => $position]);
        }
    }

    public function test_an_unknown_position_is_dispatched_as_other(): void
    {
        foreach (['unknown', 'banner', '</script>'] as $bad) {
            Livewire::test(NewsletterOptIn::class)
                ->set('position', $bad)
                ->set('email', 'visitor@example.com')
                ->call('submit')
                ->assertDispatchedBrowserEvent('newsletter-signup', ['position' => 'other']);
        }
    }

    public function test_the_default_position_is_dispatched_as_other(): void
    {
        Livewire::test(NewsletterOptIn::class)->set('email', 'visitor@example.com')->call('submit')
            ->assertDispatchedBrowserEvent('newsletter-signup', ['position' => 'other']);
    }

    public function test_an_invalid_email_dispatches_nothing_and_saves_nothing(): void
    {
        foreach (['', 'not-an-email', 'a@'] as $email) {
            Livewire::test(NewsletterOptIn::class)
                ->set('position', 'footer')
                ->set('email', $email)
                ->call('submit')
                ->assertHasErrors('email')
                ->assertNotDispatchedBrowserEvent('newsletter-signup');
        }

        $this->assertSame(0, FormEntry::count());
        $this->assertNull(session(NewsletterSignupContext::SESSION_KEY));
    }

    public function test_a_failing_browser_event_does_not_break_the_signup(): void
    {
        Livewire::component('failing-newsletter-opt-in', FailingDispatchNewsletterOptIn::class);

        $component = Livewire::test('failing-newsletter-opt-in')->set('position', 'footer')->set('email', 'visitor@example.com')->call('submit');

        $component->assertSee('Check your email for the confirm link');
        $this->assertSame(1, FormEntry::count(), 'the signup was saved');
        $this->assertSame('footer', session(NewsletterSignupContext::SESSION_KEY)['block_position'], 'the rest of the tracking still ran');
    }

    /* ------------------------------------------------------------------ the two events and the JS file */

    public function test_the_two_events_have_their_own_names(): void
    {
        $this->assertSame('newsletter_signup', NewsletterSignupContext::SIGNUP_EVENT);
        $this->assertSame('newsletter_confirmed', NewsletterSignupContext::CONFIRMED_EVENT);

        $js = file_get_contents(resource_path('js/newsletter-signup-tracking.mjs'));
        $this->assertStringContainsString("export const SIGNUP_EVENT = '" . NewsletterSignupContext::SIGNUP_EVENT . "';", $js);
        $this->assertStringContainsString("export const BROWSER_EVENT = 'newsletter-signup';", $js);
    }

    public function test_the_position_list_in_the_js_file_equals_the_php_list(): void
    {
        $js = file_get_contents(resource_path('js/newsletter-signup-tracking.mjs'));

        $this->assertSame(1, preg_match('/export const ALLOWED_POSITIONS = \[(.*?)\];/s', $js, $block), 'one list in the file');
        preg_match_all("/'([a-z_]+)'/", $block[1], $names);

        $this->assertSame(NewsletterSignupContext::POSITIONS, $names[1], 'same values in the same order, so they cannot drift apart');
        $this->assertSame(NewsletterSignupContext::MAX_URL_LENGTH, 500);
        $this->assertStringContainsString('export const MAX_URL_LENGTH = ' . NewsletterSignupContext::MAX_URL_LENGTH . ';', $js);
    }

    public function test_app_js_registers_the_one_listener_and_the_js_tests_run_with_npm_test(): void
    {
        $app = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString("import { registerNewsletterSignupTracking } from './newsletter-signup-tracking.mjs';", $app);
        $this->assertSame(1, substr_count($app, 'registerNewsletterSignupTracking(window);'), 'registered once');
        $this->assertStringNotContainsString("'newsletter-signup'", $app, 'the event name lives only in the module');

        $package = json_decode(file_get_contents(base_path('package.json')), true);
        $this->assertSame('npm run test:js', $package['scripts']['test']);
        $this->assertSame('node --test tests/js/*.test.mjs', $package['scripts']['test:js'], 'picks up every file in tests/js');
        $this->assertFileExists(base_path('tests/js/newsletter-signup-tracking.test.mjs'));
    }

    public function test_every_layout_that_shows_the_signup_form_loads_app_js(): void
    {
        // The pages that render the component extend layouts.public; the error pages and the logged-in layout include the header.
        foreach (['layouts/public', 'layouts/user', 'errors/minimal'] as $layout) {
            $this->assertStringContainsString("@vite('resources/js/app.js')", file_get_contents(resource_path('views/' . $layout . '.blade.php')), $layout);
        }
    }

    /* ------------------------------------------------------------------ the newsletter_confirmed event on /confirmed */

    public function test_confirmed_with_a_signup_context_prints_exactly_one_confirmed_event_with_both_values(): void
    {
        [$html] = $this->confirmed([NewsletterSignupContext::SESSION_KEY => ['block_position' => 'mid_article', 'page_location' => 'http://localhost/articles/sleep?x=1']]);

        $this->assertSame(1, $this->eventsIn($html));
        $this->assertStringContainsString("window.gtag('event', 'newsletter_confirmed', { page_location: \"http:\\/\\/localhost\\/articles\\/sleep?x=1\", block_position: \"mid_article\" });", $html);
        $this->assertStringContainsString("if (typeof window.gtag !== 'function') { return; }", $html, 'does nothing when gtag is not there');
        $this->assertStringContainsString('} catch (e) {}', $html, 'throws nothing');
    }

    public function test_confirmed_without_a_signup_context_prints_no_event(): void
    {
        [$html] = $this->confirmed();

        $this->assertSame(0, $this->eventsIn($html));
        $this->assertStringNotContainsString('newsletter_confirmed', $html);
        $this->assertStringNotContainsString('newsletter_signup', $html, 'the submit event is sent by the page script, never printed here');
        $this->assertStringContainsString("gtag('config', 'G-TW8JCMEBZF')", $html, 'the page itself and GA4 are as before');
    }

    public function test_a_second_load_of_confirmed_prints_no_event(): void
    {
        [$first, $sessionId] = $this->confirmed([NewsletterSignupContext::SESSION_KEY => ['block_position' => 'homepage', 'page_location' => 'http://localhost/']]);
        $this->assertSame(1, $this->eventsIn($first));
        $this->assertNotNull($sessionId);

        [$reload] = $this->confirmed([], $sessionId);

        $this->assertSame(0, $this->eventsIn($reload), 'a reload never counts twice');
    }

    public function test_the_context_is_taken_from_the_session_only_once(): void
    {
        session()->put(NewsletterSignupContext::SESSION_KEY, ['block_position' => 'footer', 'page_location' => 'http://localhost/x']);

        $this->assertSame(['block_position' => 'footer', 'page_location' => 'http://localhost/x'], NewsletterSignupContext::pull());
        $this->assertNull(NewsletterSignupContext::pull());
    }

    public function test_the_whole_flow_a_signup_then_the_confirmed_page(): void
    {
        $this->signUp('city_guide', 'http://localhost/articles/longevity-doctors-miami-fl');
        $context = session(NewsletterSignupContext::SESSION_KEY);

        [$html] = $this->confirmed([NewsletterSignupContext::SESSION_KEY => $context]);

        $this->assertSame(1, $this->eventsIn($html));
        $this->assertStringContainsString('block_position: "city_guide"', $html);
        $this->assertStringContainsString('longevity-doctors-miami-fl', $html);
    }

    public function test_hostile_values_cannot_break_out_of_the_script(): void
    {
        $evil = 'http://localhost/a?x=</script><script>alert(1)</script>&y=\'"';
        [$html] = $this->confirmed([NewsletterSignupContext::SESSION_KEY => ['block_position' => $evil, 'page_location' => $evil]]);

        $script = substr($html, strpos($html, "<script>\n(function () {\n    if (typeof window.gtag"));
        $script = substr($script, 0, strpos($script, '</script>') + 9);

        $this->assertStringNotContainsString('alert(1)</script>', $html);
        $this->assertStringNotContainsString('<script>alert(1)', $html);
        $this->assertSame(1, substr_count($script, '</script>'), 'the first closing tag is the real one');
        $this->assertStringContainsString('block_position: "other"', $script, 'the position is not whatever the session says');
        $this->assertStringContainsString('\\u003C', $script, '< is written as an escape');
        $this->assertStringContainsString('\\u0022', $script, 'quotes too');
        $this->assertStringContainsString('\\u0026', $script, 'and the ampersand');
        $this->assertStringContainsString('\\u0027', $script, 'and the apostrophe');
    }

    public function test_the_script_builder_escapes_raw_hostile_values_too(): void
    {
        $script = NewsletterSignupContext::script(['block_position' => '"</script><b>&\'', 'page_location' => '</script>"\'&']);

        $this->assertSame(1, substr_count($script, '</script>'), 'only the closing tag the builder wrote');
        $this->assertStringNotContainsString('<b>', $script);
        $this->assertDoesNotMatchRegularExpression('/[\'"]<|<\/script>.*<\/script>/s', $script);
    }

    public function test_the_event_leaves_out_the_page_when_there_is_none(): void
    {
        $script = NewsletterSignupContext::script(['block_position' => 'footer', 'page_location' => null]);

        $this->assertStringContainsString("{ block_position: \"footer\" }", $script);
        $this->assertStringNotContainsString('page_location', $script);
    }

    /* ------------------------------------------------------------------ C: /confirmed is otherwise unchanged */

    public function test_confirmed_keeps_one_noindex_tag_one_title_and_one_description_with_the_event_too(): void
    {
        [$html] = $this->confirmed([NewsletterSignupContext::SESSION_KEY => ['block_position' => 'footer', 'page_location' => 'http://localhost/']]);
        $head = substr($html, 0, strpos($html, '</head>'));

        $this->assertSame(1, preg_match_all('/<meta name="robots"/', $head));
        $this->assertStringContainsString('<meta name="robots" content="noindex, follow" />', $head);
        $this->assertSame(1, substr_count($head, '<title>'));
        $this->assertSame(1, preg_match_all('/<meta name="description"/', $head));
        $this->assertStringNotContainsString('canonical', $head);
        $this->assertSame(0, $this->eventsIn($head), 'the event is in the body, not the head');
    }
}

/**
 * A signup form whose browser event cannot be sent (used to prove a tracking problem never breaks a signup).
 */
class FailingDispatchNewsletterOptIn extends NewsletterOptIn
{
    public function dispatchBrowserEvent($event, $data = null)
    {
        throw new \RuntimeException('the browser event failed');
    }
}
