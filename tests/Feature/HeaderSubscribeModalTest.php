<?php

namespace Tests\Feature;

use App\Http\Livewire\Forms\NewsletterOptIn;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * DEV-14. The header subscribe pop-up copy. The wording is passed to the shared newsletter form as optional props,
 * so every other place that uses the form (the hamburger menu, the article footer box, the homepage) is unchanged.
 * The content table is created here (in-memory SQLite from phpunit.xml) so no real database is touched.
 */
class HeaderSubscribeModalTest extends TestCase
{
    private const LINE = "What's working. What's hype. What we're trying ourselves.";
    private const NOTE = "We won't share your info. Unsubscribe anytime.";

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

    /** The whole header pop-up, through the end of its form. Entities decoded so apostrophes compare as typed. */
    private function modal(): string
    {
        $html = $this->get('/confirmed')->assertOk()->getContent();
        $start = strpos($html, 'id="header-subscribe-modal"');
        $this->assertNotFalse($start, 'the pop-up is in the header for guests');

        return html_entity_decode(substr($html, $start, strpos($html, '</form>', $start) - $start + 7), ENT_QUOTES);
    }

    /** The newsletter form in the hamburger menu: a different place that uses the same component. */
    private function menuForm(): string
    {
        $html = $this->get('/confirmed')->assertOk()->getContent();
        $start = strpos($html, 'id="primary-nav"');
        $this->assertNotFalse($start);

        return html_entity_decode(substr($html, $start, strpos($html, '</form>', $start) - $start + 7), ENT_QUOTES);
    }

    public function test_the_modal_title_and_line_under_it_are_the_new_copy(): void
    {
        $modal = $this->modal();

        $this->assertStringContainsString('id="header-subscribe-modal-label">The longevity insider email, every Thursday</div>', $modal);
        $this->assertStringContainsString('<p class="small text-muted mb-3">' . self::LINE . '</p>', $modal);
        $this->assertLessThan(strpos($modal, self::LINE), strpos($modal, 'The longevity insider email, every Thursday'), 'the line follows the title');
    }

    public function test_the_reassurance_line_sits_between_the_email_field_and_the_button(): void
    {
        $modal = $this->modal();

        $this->assertStringContainsString('<p class="form-note small text-muted mb-3">' . self::NOTE . '</p>', $modal);
        $this->assertSame(1, substr_count($modal, '<p class="form-note'), 'one rendered note');
        $note = strpos($modal, '<p class="form-note');
        $this->assertGreaterThan(strpos($modal, 'placeholder="Your best email"'), $note, 'after the email field');
        $this->assertLessThan(strpos($modal, 'Count me in</button>'), $note, 'above the button');
    }

    public function test_the_placeholder_and_button_text_are_the_new_copy(): void
    {
        $modal = $this->modal();

        $this->assertStringContainsString('placeholder="Your best email"', $modal);
        $this->assertStringContainsString('<button type="submit" class="btn btn-outline-primary">Count me in</button>', $modal);
        $this->assertStringContainsString('aria-label="Email"', $modal, 'the accessible name is unchanged');
    }

    public function test_the_old_title_lines_and_button_text_are_gone_from_the_modal(): void
    {
        $modal = $this->modal();

        foreach ([
            'Get the longevity insider email',
            'Sign Up For Our Newsletter',
            'Weekly insights into the future of longevity',
            "Don't worry, we won't share your info.",
            "What's working in longevity?",
            'We try it ourselves',
            'placeholder="Email"',
            '>Sign Up</button>',
        ] as $old) {
            $this->assertStringNotContainsString($old, $modal, $old);
        }
    }

    public function test_the_modal_copy_has_no_em_dash_or_en_dash(): void
    {
        $this->assertSame(0, preg_match('/[\x{2012}-\x{2015}]/u', $this->modal()));
    }

    public function test_the_signup_still_goes_to_the_header_source_and_the_header_button_text_is_untouched(): void
    {
        $html = $this->get('/confirmed')->assertOk()->getContent();
        $modal = $this->modal();

        $this->assertStringContainsString('class="header-subscribe-form"', $modal);
        $this->assertStringContainsString('Newsletter Header', html_entity_decode($html, ENT_QUOTES), 'the tracking source name is passed through');
        $this->assertStringContainsString('<span>Join the Insiders</span>', $html);
    }

    public function test_the_hamburger_menu_form_still_says_sign_up_and_email(): void
    {
        $menu = $this->menuForm();

        $this->assertStringContainsString('placeholder="Email"', $menu);
        $this->assertStringContainsString('<button type="submit" class="btn btn-outline-primary">Sign Up</button>', $menu);
        $this->assertStringNotContainsString('Your best email', $menu);
        $this->assertStringNotContainsString('Count me in', $menu);
        $this->assertStringNotContainsString('form-note', $menu);
        $this->assertStringNotContainsString('We won\'t share your info', $menu);
    }

    public function test_the_component_defaults_are_the_old_text_and_print_no_note(): void
    {
        $component = Livewire::test(NewsletterOptIn::class);

        $component->assertSet('buttonText', 'Sign Up')
            ->assertSet('emailPlaceholder', 'Email')
            ->assertSet('note', '')
            ->assertSee('Sign Up')
            ->assertSeeHtml('placeholder="Email"')
            ->assertDontSeeHtml('form-note');
    }

    public function test_the_props_change_only_the_wording(): void
    {
        $component = Livewire::test(NewsletterOptIn::class, [
            'buttonText' => 'Count me in',
            'emailPlaceholder' => 'Your best email',
            'note' => self::NOTE,
        ]);

        $component->assertSeeHtml('placeholder="Your best email"')
            ->assertSee('Count me in')
            ->assertSee('Unsubscribe anytime.')
            ->assertSet('form', \App\Models\FormEntry::FORM_NEWSLETTER_SIDEBAR);
    }

    public function test_an_empty_note_prints_nothing_extra(): void
    {
        $default = Livewire::test(NewsletterOptIn::class)->lastRenderedDom;
        $blankNote = Livewire::test(NewsletterOptIn::class, ['note' => '   '])->lastRenderedDom;

        $this->assertStringNotContainsString('form-note', $blankNote);
        $this->assertSame(substr_count($default, '<'), substr_count($blankNote, '<'));
    }

    public function test_the_validation_message_still_shows_for_a_bad_email(): void
    {
        Livewire::test(NewsletterOptIn::class, ['buttonText' => 'Count me in'])
            ->set('email', 'not-an-email')
            ->call('submit')
            ->assertHasErrors(['email']);
    }
}
