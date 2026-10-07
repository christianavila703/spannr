<?php

namespace Tests\Feature;

use App\Http\Livewire\Admin\FormEntries\FundingApplicantIndex;
use App\Http\Livewire\Admin\FormEntries\InvestorOptInIndex;
use App\Http\Livewire\Admin\FormEntries\NewsletterSubscribers;
use App\Http\Livewire\Admin\FormEntries\WebsiteContactIndex;
use App\Http\Livewire\Forms\NewsletterOptIn as NewsletterOptInForm;
use App\Jobs\FormNotifications\InvestorOptIn;
use App\Jobs\FormNotifications\NewsletterOptIn as MailchimpNewsletterJob;
use App\Jobs\FormNotifications\ResubmitMailchimpApplicableForms;
use App\Jobs\FormNotifications\SubscribeToBeehiiv;
use App\Models\FormEntry;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Mockery;
use Monolog\Handler\TestHandler;
use RuntimeException;
use Tests\TestCase;
use TypeError;

/**
 * Every test uses Http::fake. No request ever leaves the machine.
 * The form_entries table is created here (in-memory SQLite from phpunit.xml) so no real database is touched.
 *
 * The servers run QUEUE_CONNECTION=sync, so the beehiiv call is not queued. FormEntry runs it with
 * dispatchAfterResponse(). In a test the "after the response" phase is simulated by settle(),
 * which is what Laravel's kernel does in terminate() once the response has been sent.
 */
class BeehiivSignupTest extends TestCase
{
    private const URL = 'https://api.beehiiv.com/v2/publications/pub_test/subscriptions';

    private const SUCCESS_MESSAGE = "Hey, nice. One more tap and you're in. Check your email for the confirm link, that's where the good stuff starts. Not there in a minute? Peek in spam or promotions.";

    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

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

        config([
            'services.beehiiv.key' => 'test-key-SECRET',
            'services.beehiiv.publication_id' => 'pub_test',
            'logging.default' => 'testlog',
            'logging.channels.testlog' => ['driver' => 'monolog', 'handler' => TestHandler::class],
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Runs whatever was registered to run after the response, then forgets it so it cannot run twice.
     */
    private function settle(): void
    {
        $this->app->terminate();

        (function () {
            $this->terminatingCallbacks = [];
        })->call($this->app);
    }

    private function entry(array $overrides = [], bool $fireEvents = true, bool $settle = true): FormEntry
    {
        $attributes = array_merge([
            'form' => FormEntry::FORM_NEWSLETTER_SIDEBAR,
            'email' => 'visitor@example.com',
            'url' => 'https://spannr.com/articles/some-article',
            'fields' => json_encode(['email' => 'visitor@example.com']),
        ], $overrides);

        $entry = $fireEvents
            ? FormEntry::create($attributes)
            : FormEntry::withoutEvents(fn () => FormEntry::create($attributes));

        if ($fireEvents && $settle) {
            $this->settle();
        }

        return $entry;
    }

    private function beehiiv(FormEntry $entry): array
    {
        $record = [];

        foreach (json_decode((string) $entry->fresh()->fields, true) as $key => $value) {
            if (str_starts_with($key, 'beehiiv_')) {
                $record[substr($key, strlen('beehiiv_'))] = $value;
            }
        }

        return $record;
    }

    private function logText(): string
    {
        $handler = Log::channel('testlog')->getLogger()->getHandlers()[0];

        return collect($handler->getRecords())->map(fn ($record) => $record['message'] . json_encode($record['context']))->implode("\n");
    }

    /* ------------------------------------------------------------------ after the response */

    public function test_the_beehiiv_call_runs_after_the_response_not_during_the_request(): void
    {
        Http::fake([self::URL => Http::response(['data' => ['status' => 'active']], 200)]);

        Route::post('/__t/newsletter', function () {
            FormEntry::create([
                'form' => FormEntry::FORM_NEWSLETTER_SIDEBAR,
                'email' => 'visitor@example.com',
                'url' => 'https://spannr.com/x',
                'fields' => json_encode(['email' => 'visitor@example.com']),
            ]);

            return response('Thanks');
        });

        // This is exactly what public/index.php does: handle(), send the response, then terminate().
        $kernel = app(Kernel::class);
        $request = HttpRequest::create('/__t/newsletter', 'POST');

        $response = $kernel->handle($request);

        $this->assertSame('Thanks', $response->getContent());
        Http::assertNothingSent();
        $this->assertSame([], $this->beehiiv(FormEntry::first()), 'nothing is recorded while the response is still being built');

        $kernel->terminate($request, $response);

        Http::assertSentCount(1);
        $this->assertSame('sent', $this->beehiiv(FormEntry::first())['status']);
    }

    public function test_the_form_hook_registers_after_response_work_and_never_a_normal_dispatch(): void
    {
        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatchAfterResponse')->once()->with(Mockery::type(SubscribeToBeehiiv::class));
        $dispatcher->shouldNotReceive('dispatch');
        $dispatcher->shouldNotReceive('dispatchSync');
        $dispatcher->shouldNotReceive('dispatchNow');
        $this->app->instance(Dispatcher::class, $dispatcher);
        Http::fake();

        $this->entry([], true, false);

        Http::assertNothingSent();
    }

    public function test_one_request_with_a_2_second_connect_and_5_second_total_timeout(): void
    {
        $options = null;
        Http::fake(function ($request, $requestOptions) use (&$options) {
            $options = $requestOptions;

            return Http::response(['data' => ['status' => 'active']], 200);
        });

        $this->entry();

        $this->assertEquals(2, $options['connect_timeout']);
        $this->assertEquals(5, $options['timeout']);
    }

    /* ------------------------------------------------------------------ success */

    public function test_success_sends_the_expected_request_and_records_it(): void
    {
        Http::fake([self::URL => Http::response(['data' => ['id' => 'sub_1', 'status' => 'active']], 200)]);

        $entry = $this->entry();

        Http::assertSentCount(1);
        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST'
                && $request->url() === self::URL
                && $request->hasHeader('Authorization', 'Bearer test-key-SECRET')
                && $request->data() === [
                    'email' => 'visitor@example.com',
                    'utm_source' => 'newsletter-sidebar',
                    'utm_medium' => 'website',
                    'send_welcome_email' => false,
                    'reactivate_existing' => false,
                    'referring_site' => 'https://spannr.com/articles/some-article',
                ]
                && ! array_key_exists('utm_campaign', $request->data());
        });

        $this->assertSame('sent', $this->beehiiv($entry)['status']);
        $this->assertSame(200, $this->beehiiv($entry)['http_status']);
        $this->assertSame('active', $this->beehiiv($entry)['subscription_status']);
        $this->assertArrayHasKey('at', $this->beehiiv($entry));
    }

    public function test_utm_source_follows_the_form_name(): void
    {
        Http::fake([self::URL => Http::response(['data' => ['status' => 'active']], 200)]);

        $this->entry(['form' => FormEntry::FORM_NEWSLETTER_HEADER]);

        Http::assertSent(fn (Request $request) => $request['utm_source'] === 'newsletter-header');
    }

    public function test_existing_subscriber_is_treated_as_done_after_a_single_request(): void
    {
        // The docs only list 200 for success and say nothing about existing subscribers,
        // so any 2xx is treated as done whatever the subscription status says.
        Http::fake([self::URL => Http::response(['data' => ['id' => 'sub_9', 'status' => 'inactive']], 200)]);

        $entry = $this->entry();

        Http::assertSentCount(1);
        $this->assertSame('sent', $this->beehiiv($entry)['status']);
        $this->assertSame('inactive', $this->beehiiv($entry)['subscription_status']);
    }

    public function test_blank_key_skips_and_records_it(): void
    {
        config(['services.beehiiv.key' => '']);
        Http::fake();

        $entry = $this->entry();

        Http::assertNothingSent();
        $this->assertSame('skipped_no_credentials', $this->beehiiv($entry)['status']);
    }

    public function test_blank_publication_id_skips_and_records_it(): void
    {
        config(['services.beehiiv.publication_id' => null]);
        Http::fake();

        $entry = $this->entry();

        Http::assertNothingSent();
        $this->assertSame('skipped_no_credentials', $this->beehiiv($entry)['status']);
    }

    /* ------------------------------------------------------------------ every failure is recorded as failed */

    /**
     * @dataProvider singleRequestFailures
     */
    public function test_every_other_error_response_is_recorded_as_failed_after_exactly_one_request(int $status): void
    {
        Http::fake([self::URL => Http::response(['status' => $status, 'errors' => [['message' => 'x', 'code' => 'y']]], $status, ['RateLimit-Reset' => time() + 30])]);

        $entry = $this->entry();

        Http::assertSentCount(1);
        $this->assertSame('failed', $this->beehiiv($entry)['status']);
        $this->assertSame($status, $this->beehiiv($entry)['http_status']);
    }

    public static function singleRequestFailures(): array
    {
        return array_map(fn ($status) => [$status], [401, 403, 404, 429, 500, 502, 503]);
    }

    /**
     * @dataProvider malformedRequestStatuses
     */
    public function test_a_400_or_422_gets_exactly_one_more_request_with_only_the_email_and_ends_up_sent(int $status): void
    {
        Http::fake([self::URL => Http::sequence()->push(['status' => $status], $status)->push(['data' => ['status' => 'active']], 200)]);

        $entry = $this->entry();

        Http::assertSentCount(2);
        $sent = Http::recorded();

        $this->assertSame([
            'email' => 'visitor@example.com',
            'utm_source' => 'newsletter-sidebar',
            'utm_medium' => 'website',
            'send_welcome_email' => false,
            'reactivate_existing' => false,
            'referring_site' => 'https://spannr.com/articles/some-article',
        ], $sent[0][0]->data(), 'the first request carries the optional fields');

        $this->assertSame(
            ['email' => 'visitor@example.com', 'send_welcome_email' => false, 'reactivate_existing' => false],
            $sent[1][0]->data(),
            'the second request is exactly the email-only one'
        );
        $this->assertSame(self::URL, $sent[1][0]->url());
        $this->assertTrue($sent[1][0]->hasHeader('Authorization', 'Bearer test-key-SECRET'));

        // Only the final outcome is recorded: sent, with the 200, and no trace of the 400 or 422.
        $record = $this->beehiiv($entry);
        $this->assertSame('sent', $record['status']);
        $this->assertSame(200, $record['http_status']);
        $this->assertEqualsCanonicalizing(['status', 'http_status', 'subscription_status', 'at'], array_keys($record));
    }

    /**
     * @dataProvider malformedRequestStatuses
     */
    public function test_a_400_or_422_twice_is_recorded_as_failed_after_two_requests(int $status): void
    {
        Http::fake([self::URL => Http::response(['status' => $status], $status)]);

        $entry = $this->entry();

        Http::assertSentCount(2);
        $this->assertSame('failed', $this->beehiiv($entry)['status']);
        $this->assertSame($status, $this->beehiiv($entry)['http_status']);
    }

    public static function malformedRequestStatuses(): array
    {
        return [[400], [422]];
    }

    public function test_after_a_400_and_a_500_only_the_final_outcome_is_recorded(): void
    {
        Http::fake([self::URL => Http::sequence()->push(['status' => 400], 400)->push(['status' => 500], 500)]);

        $entry = $this->entry();

        Http::assertSentCount(2);
        $this->assertSame('failed', $this->beehiiv($entry)['status']);
        $this->assertSame(500, $this->beehiiv($entry)['http_status'], 'the 500 is the final outcome, not the 400');
    }

    public function test_after_a_400_and_a_timeout_only_the_final_outcome_is_recorded(): void
    {
        $calls = 0;
        Http::fake(function () use (&$calls) {
            return ++$calls === 1 ? Http::response(['status' => 400], 400) : throw new ConnectionException('cURL error 28');
        });

        $entry = $this->entry();

        $this->assertSame(2, $calls);
        $this->assertSame('failed', $this->beehiiv($entry)['status']);
        $this->assertArrayNotHasKey('http_status', $this->beehiiv($entry));
    }

    public function test_both_requests_use_the_same_2_second_connect_and_5_second_total_timeouts(): void
    {
        $seen = [];
        Http::fake(function ($request, $options) use (&$seen) {
            $seen[] = [$options['connect_timeout'], $options['timeout']];

            return count($seen) === 1 ? Http::response(['status' => 400], 400) : Http::response(['data' => ['status' => 'active']], 200);
        });

        $this->entry();

        $this->assertEquals([[2, 5], [2, 5]], $seen);
    }

    public function test_a_timeout_or_connection_error_is_recorded_as_failed(): void
    {
        Http::fake(fn () => throw new ConnectionException('cURL error 28: Operation timed out'));

        $entry = $this->entry();

        $this->assertSame('failed', $this->beehiiv($entry)['status']);
        $this->assertArrayNotHasKey('http_status', $this->beehiiv($entry));
    }

    /**
     * @dataProvider unexpectedExceptions
     */
    public function test_an_unexpected_exception_is_recorded_as_failed_and_never_thrown(string $exceptionClass): void
    {
        Http::fake(fn () => throw new $exceptionClass('boom'));
        $entry = $this->entry([], false);

        (new SubscribeToBeehiiv($entry))->handle(); // would fail the test if it threw

        $this->assertSame('failed', $this->beehiiv($entry)['status']);
    }

    public static function unexpectedExceptions(): array
    {
        return [[RuntimeException::class], [TypeError::class]];
    }

    public function test_the_last_resort_catch_records_failed_when_something_throws_outside_the_http_call(): void
    {
        Http::fake();
        $entry = $this->entry([], false);

        // Not a string: converting it to the string the HTTP method wants throws, outside the http try/catch.
        config(['services.beehiiv.key' => new class {
            public function __toString(): string
            {
                throw new \LogicException('unexpected');
            }
        }]);

        (new SubscribeToBeehiiv($entry))->handle(); // would fail the test if it threw

        Http::assertNothingSent();
        $this->assertSame('failed', $this->beehiiv($entry)['status']);
    }

    public function test_handle_never_throws_and_still_works_when_the_logger_itself_breaks(): void
    {
        Http::fake([self::URL => Http::response(['data' => ['status' => 'active']], 200)]);
        $entry = $this->entry([], false);

        Log::shouldReceive('info')->andThrow(new RuntimeException('log disk full'));
        Log::shouldReceive('warning')->andThrow(new RuntimeException('log disk full'));

        (new SubscribeToBeehiiv($entry))->handle(); // would fail the test if it threw

        $this->assertSame('sent', $this->beehiiv($entry)['status'], 'a broken logger must not stop the signup being sent and recorded');
    }

    public function test_handle_never_throws_when_the_logger_is_broken_and_beehiiv_fails_too(): void
    {
        Http::fake(fn () => throw new ConnectionException('cURL error 28'));
        $entry = $this->entry([], false);

        Log::shouldReceive('info')->andThrow(new RuntimeException('log disk full'));
        Log::shouldReceive('warning')->andThrow(new RuntimeException('log disk full'));

        (new SubscribeToBeehiiv($entry))->handle();

        $this->assertSame('failed', $this->beehiiv($entry)['status']);
    }

    public function test_handle_never_throws_even_when_recording_fails(): void
    {
        Http::fake([self::URL => Http::response(['status' => 500], 500)]);
        $entry = $this->entry([], false);

        Schema::drop('form_entries');

        (new SubscribeToBeehiiv($entry))->handle();

        $this->assertStringContainsString('could not record beehiiv status', $this->logText());
    }

    public function test_logs_contain_only_the_entry_id_and_status(): void
    {
        Http::fake([self::URL => Http::response(['status' => 500], 500)]);
        $this->entry();

        Http::fake(fn () => throw new ConnectionException('cURL error 28 for https://api.beehiiv.com/v2/publications/pub_test/subscriptions'));
        $this->entry(['email' => 'second@example.com']);

        $logs = $this->logText();

        $this->assertStringContainsString('FormEntry 1', $logs);
        $this->assertStringContainsString('beehiiv HTTP 500', $logs);
        foreach (['test-key-SECRET', 'visitor@example.com', 'second@example.com', 'pub_test', 'Bearer'] as $secret) {
            $this->assertStringNotContainsString($secret, $logs);
        }
    }

    /* ------------------------------------------------------------------ the visitor */

    /**
     * @dataProvider beehiivOutcomes
     */
    public function test_the_visitor_sees_the_same_success_message_whatever_beehiiv_does(string $scenario): void
    {
        match ($scenario) {
            'ok' => Http::fake([self::URL => Http::response(['data' => ['status' => 'active']], 200)]),
            'server error' => Http::fake([self::URL => Http::response(['status' => 500], 500)]),
            'rate limited' => Http::fake([self::URL => Http::response(['status' => 429], 429)]),
            'unauthorized' => Http::fake([self::URL => Http::response(['status' => 401], 401)]),
            'timeout' => Http::fake(fn () => throw new ConnectionException('cURL error 28')),
            'unexpected exception' => Http::fake(fn () => throw new RuntimeException('boom')),
            'blank key' => config(['services.beehiiv.key' => '']),
            'registering the after-response work fails' => $this->failTheDispatcher(),
        };

        Livewire::test(NewsletterOptInForm::class)
            ->set('email', 'visitor@example.com')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('showSuccess', true)
            ->assertSee(self::SUCCESS_MESSAGE, false)
            ->assertDontSee('Thanks for subscribing!');

        // Livewire's test runner terminates the app after each call, so the after-response work
        // has already run here: it must not have blown up, and the signup is always saved.
        $this->assertSame(1, FormEntry::count(), 'the signup is always saved');
    }

    public static function beehiivOutcomes(): array
    {
        return array_map(fn ($name) => [$name], [
            'ok', 'server error', 'rate limited', 'unauthorized', 'timeout', 'unexpected exception', 'blank key', 'registering the after-response work fails',
        ]);
    }

    private function failTheDispatcher(): void
    {
        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatchAfterResponse')->andThrow(new RuntimeException('cannot register'));
        $this->app->instance(Dispatcher::class, $dispatcher);
    }

    public function test_the_real_form_keeps_its_original_fields_next_to_the_beehiiv_record(): void
    {
        Http::fake([self::URL => Http::response(['data' => ['status' => 'active']], 200)]);

        Livewire::test(NewsletterOptInForm::class)
            ->set('form', FormEntry::FORM_NEWSLETTER_ABOUT)
            ->set('first_name', 'Ann')
            ->set('last_name', 'Lee')
            ->set('email', 'ann@example.com')
            ->call('submit')
            ->assertSet('showSuccess', true);

        $fields = json_decode((string) FormEntry::first()->fields, true);

        foreach (['first_name', 'last_name', 'email', 'url', 'ip'] as $key) {
            $this->assertArrayHasKey($key, $fields, "original key $key must survive");
        }
        $this->assertSame('Ann', $fields['first_name']);
        $this->assertSame('Lee', $fields['last_name']);
        $this->assertSame('ann@example.com', $fields['email']);
        $this->assertSame('sent', $fields['beehiiv_status']);
        $this->assertSame(200, $fields['beehiiv_http_status']);
        Http::assertSent(fn (Request $request) => $request['utm_source'] === 'newsletter-about' && $request['email'] === 'ann@example.com');
    }

    /* ------------------------------------------------------------------ Mailchimp is out of the newsletter path */

    public function test_newsletter_signups_no_longer_dispatch_the_mailchimp_job(): void
    {
        Bus::fake([MailchimpNewsletterJob::class]);
        Http::fake([self::URL => Http::response(['data' => ['status' => 'active']], 200)]);

        foreach (FormEntry::NEWSLETTER_FORMS as $form) {
            $this->entry(['form' => $form, 'email' => strtolower(str_replace(' ', '', $form)) . '@example.com']);
        }

        Bus::assertNotDispatched(MailchimpNewsletterJob::class);
        Http::assertSentCount(count(FormEntry::NEWSLETTER_FORMS));
    }

    public function test_the_daily_mailchimp_resubmit_ignores_newsletter_rows_but_still_handles_other_forms(): void
    {
        Bus::fake([MailchimpNewsletterJob::class, InvestorOptIn::class]);

        $this->entry(['form' => FormEntry::FORM_NEWSLETTER_SIDEBAR], false);
        $this->entry(['form' => FormEntry::FORM_NEWSLETTER_HOMEPAGE_HERO, 'email' => 'b@example.com'], false);
        $this->entry(['form' => FormEntry::FORM_INVESTOR_OPTIN, 'email' => 'c@example.com'], false);

        (new ResubmitMailchimpApplicableForms)->handle();

        Bus::assertNotDispatched(MailchimpNewsletterJob::class);
        Bus::assertDispatched(InvestorOptIn::class);
    }

    /* ------------------------------------------------------------------ beehiiv:backfill */

    public function test_backfill_dry_run_counts_and_masks_and_sends_nothing(): void
    {
        Http::fake();
        $this->entry(['email' => 'alice@example.com'], false);
        $this->entry(['email' => 'ALICE@example.com', 'form' => FormEntry::FORM_NEWSLETTER_HEADER], false);
        $this->entry(['email' => 'bob@example.org', 'form' => FormEntry::FORM_NEWSLETTER_ABOUT], false);
        $this->entry(['email' => 'not-an-email'], false);
        $this->entry(['email' => 'done@example.com', 'fields' => json_encode(['beehiiv_status' => 'sent'])], false);
        $this->entry(['email' => 'contact@example.com', 'form' => FormEntry::FORM_CONTACT], false);

        $this->assertSame(0, Artisan::call('beehiiv:backfill'));
        $output = Artisan::output();

        $this->assertStringContainsString('Newsletter signup rows (all newsletter forms): 5', $output);
        $this->assertStringContainsString('Already sent to beehiiv: 1', $output);
        $this->assertStringContainsString('Skipped, same email already handled: 1', $output);
        $this->assertStringContainsString('Skipped, not a valid email: 1', $output);
        $this->assertStringContainsString('Would send: 2 distinct emails', $output);
        $this->assertStringContainsString('a***@example.com', $output);
        $this->assertStringContainsString('Dry run only', $output);
        foreach (['alice@example.com', 'bob@example.org', 'done@example.com', 'contact@example.com'] as $email) {
            $this->assertStringNotContainsString($email, $output);
        }
        Http::assertNothingSent();
    }

    public function test_backfill_send_is_resumable_and_skips_rows_already_sent(): void
    {
        Http::fake([self::URL => Http::response(['data' => ['status' => 'active']], 200)]);
        $this->entry(['email' => 'alice@example.com'], false);
        $this->entry(['email' => 'bob@example.org'], false);
        $this->entry(['email' => 'done@example.com', 'fields' => json_encode(['beehiiv_status' => 'sent'])], false);
        $this->entry(['email' => 'skipped@example.com', 'fields' => json_encode(['beehiiv_status' => 'skipped_no_credentials'])], false);

        $this->assertSame(0, Artisan::call('beehiiv:backfill', ['--send' => true, '--all-time' => true, '--delay' => 0, '--limit' => 1]));
        Http::assertSentCount(1);

        $this->assertSame(0, Artisan::call('beehiiv:backfill', ['--send' => true, '--all-time' => true, '--delay' => 0]));
        Http::assertSentCount(3); // the remaining two (bob, skipped), never done@example.com, never alice twice

        $this->assertSame(0, Artisan::call('beehiiv:backfill', ['--send' => true, '--all-time' => true, '--delay' => 0]));
        Http::assertSentCount(3);
        $this->assertStringContainsString('Would send: 0 distinct emails', Artisan::output());
        Http::assertNotSent(fn (Request $request) => $request['email'] === 'done@example.com');
    }

    public function test_backfill_send_refuses_to_run_without_since_or_all_time(): void
    {
        Http::fake();
        $this->entry(['email' => 'alice@example.com'], false);

        $this->assertSame(1, Artisan::call('beehiiv:backfill', ['--send' => true, '--delay' => 0]));
        $output = Artisan::output();

        $this->assertStringContainsString('--send needs a limit', $output);
        $this->assertStringContainsString('Nothing was sent', $output);
        $this->assertStringNotContainsString('alice@example.com', $output);
        Http::assertNothingSent();
        $this->assertSame([], $this->beehiiv(FormEntry::first()), 'no row may be marked');
    }

    public function test_backfill_dry_run_still_works_without_either_flag_and_warns(): void
    {
        Http::fake();
        $this->entry(['email' => 'alice@example.com'], false);

        $this->assertSame(0, Artisan::call('beehiiv:backfill'));
        $output = Artisan::output();

        $this->assertStringContainsString('No --since date', $output);
        $this->assertStringContainsString('Would send: 1 distinct emails', $output);
        Http::assertNothingSent();
    }

    public function test_backfill_all_time_sends_everything_and_since_is_not_required(): void
    {
        Http::fake([self::URL => Http::response(['data' => ['status' => 'active']], 200)]);
        $this->entry(['email' => 'old@example.com', 'created_at' => '2024-01-05 10:00:00'], false);
        $this->entry(['email' => 'new@example.com', 'created_at' => '2026-10-08 10:00:00'], false);

        $this->assertSame(0, Artisan::call('beehiiv:backfill', ['--send' => true, '--all-time' => true, '--delay' => 0]));

        Http::assertSentCount(2);
        Http::assertSent(fn (Request $request) => $request['email'] === 'old@example.com');
    }

    public function test_backfill_rejects_since_together_with_all_time(): void
    {
        Http::fake();
        $this->entry(['email' => 'alice@example.com'], false);

        $this->assertSame(1, Artisan::call('beehiiv:backfill', ['--send' => true, '--since' => '2026-10-07', '--all-time' => true, '--delay' => 0]));

        $this->assertStringContainsString('either --since or --all-time', Artisan::output());
        Http::assertNothingSent();
    }

    public function test_backfill_since_ignores_older_signups_and_warns_when_not_given(): void
    {
        Http::fake([self::URL => Http::response(['data' => ['status' => 'active']], 200)]);
        $this->entry(['email' => 'old@example.com', 'created_at' => '2026-01-05 10:00:00'], false);
        $this->entry(['email' => 'new@example.com', 'created_at' => '2026-10-08 10:00:00'], false);

        Artisan::call('beehiiv:backfill');
        $withoutSince = Artisan::output(); // the buffer is cleared once read, so read it once
        $this->assertStringContainsString('No --since date', $withoutSince);
        $this->assertStringContainsString('Would send: 2 distinct emails', $withoutSince);

        Artisan::call('beehiiv:backfill', ['--since' => '2026-10-07']);
        $withSince = Artisan::output();
        $this->assertStringContainsString('Would send: 1 distinct emails', $withSince);
        $this->assertStringNotContainsString('No --since date', $withSince);

        Artisan::call('beehiiv:backfill', ['--send' => true, '--delay' => 0, '--since' => '2026-10-07']);
        Http::assertSentCount(1);
        Http::assertSent(fn (Request $request) => $request['email'] === 'new@example.com');

        $this->assertSame(1, Artisan::call('beehiiv:backfill', ['--since' => 'not a date at all']));
    }

    public function test_backfill_send_without_credentials_does_nothing(): void
    {
        config(['services.beehiiv.key' => '']);
        Http::fake();
        $this->entry(['email' => 'alice@example.com'], false);

        $this->assertSame(1, Artisan::call('beehiiv:backfill', ['--send' => true, '--all-time' => true, '--delay' => 0]));

        Http::assertNothingSent();
        $this->assertSame([], $this->beehiiv(FormEntry::first()));
    }

    public function test_backfill_stops_after_three_failures_in_a_row(): void
    {
        Http::fake([self::URL => Http::response(['status' => 401], 401)]);
        foreach (['a', 'b', 'c', 'd', 'e'] as $name) {
            $this->entry(['email' => "$name@example.com"], false);
        }

        $this->assertSame(1, Artisan::call('beehiiv:backfill', ['--send' => true, '--all-time' => true, '--delay' => 0]));

        Http::assertSentCount(3);
        $this->assertStringContainsString('Stopping after 3 failures in a row', Artisan::output());
    }

    /* ------------------------------------------------------------------ beehiiv:backfill --retry-recorded */

    private function retryFixtures(): void
    {
        $this->entry(['email' => 'failed@example.com', 'fields' => json_encode(['beehiiv_status' => 'failed', 'beehiiv_http_status' => 500])], false);
        $this->entry(['email' => 'skipped@example.com', 'form' => FormEntry::FORM_NEWSLETTER_HEADER, 'fields' => json_encode(['beehiiv_status' => 'skipped_no_credentials'])], false);
        $this->entry(['email' => 'sent@example.com', 'fields' => json_encode(['beehiiv_status' => 'sent'])], false);
        $this->entry(['email' => 'mailchimpera@example.com', 'created_at' => '2023-03-01 10:00:00'], false); // no beehiiv record at all
        $this->entry(['email' => 'contactfailed@example.com', 'form' => FormEntry::FORM_CONTACT, 'fields' => json_encode(['beehiiv_status' => 'failed'])], false); // not a newsletter form
    }

    public function test_retry_recorded_dry_run_needs_no_other_flag_masks_emails_and_sends_nothing(): void
    {
        Http::fake();
        $this->retryFixtures();

        $this->assertSame(0, Artisan::call('beehiiv:backfill', ['--retry-recorded' => true]));
        $output = Artisan::output();

        $this->assertStringContainsString('Would send: 2 distinct emails', $output);
        $this->assertStringContainsString('Retry mode', $output);
        $this->assertStringContainsString('Dry run only', $output);
        $this->assertStringNotContainsString('No --since date', $output, 'no historical-signup warning in retry mode');
        $this->assertMatchesRegularExpression('/Sample \(masked\): [fs]\*\*\*@example\.com, [fs]\*\*\*@example\.com/', $output);
        foreach (['failed@example.com', 'skipped@example.com', 'sent@example.com', 'mailchimpera@example.com', 'contactfailed@example.com'] as $email) {
            $this->assertStringNotContainsString($email, $output);
        }
        Http::assertNothingSent();
    }

    public function test_retry_recorded_send_only_resends_failed_and_skipped_rows(): void
    {
        Http::fake([self::URL => Http::response(['data' => ['status' => 'active']], 200)]);
        $this->retryFixtures();

        $this->assertSame(0, Artisan::call('beehiiv:backfill', ['--retry-recorded' => true, '--send' => true, '--delay' => 0]));

        Http::assertSentCount(2);
        Http::assertSent(fn (Request $request) => $request['email'] === 'failed@example.com');
        Http::assertSent(fn (Request $request) => $request['email'] === 'skipped@example.com');
        Http::assertNotSent(fn (Request $request) => in_array($request['email'], ['sent@example.com', 'mailchimpera@example.com', 'contactfailed@example.com'], true));

        $rows = FormEntry::all()->keyBy('email');
        $this->assertSame('sent', $this->beehiiv($rows['failed@example.com'])['status']);
        $this->assertSame('sent', $this->beehiiv($rows['skipped@example.com'])['status']);
        $this->assertSame([], $this->beehiiv($rows['mailchimpera@example.com']), 'old Mailchimp-era row is never touched');

        // Everything is sent now, so a second run finds nothing.
        $this->assertSame(0, Artisan::call('beehiiv:backfill', ['--retry-recorded' => true, '--send' => true, '--delay' => 0]));
        $this->assertStringContainsString('Would send: 0 distinct emails', Artisan::output());
        Http::assertSentCount(2);
    }

    public function test_retry_recorded_gets_the_same_email_only_retry_on_a_400(): void
    {
        Http::fake([self::URL => Http::sequence()->push(['status' => 400], 400)->push(['data' => ['status' => 'active']], 200)]);
        $row = $this->entry(['email' => 'failed@example.com', 'fields' => json_encode(['beehiiv_status' => 'failed', 'beehiiv_http_status' => 400])], false);

        $this->assertSame(0, Artisan::call('beehiiv:backfill', ['--retry-recorded' => true, '--send' => true, '--delay' => 0]));

        Http::assertSentCount(2);
        $sent = Http::recorded();
        $this->assertArrayHasKey('utm_source', $sent[0][0]->data());
        $this->assertSame(['email' => 'failed@example.com', 'send_welcome_email' => false, 'reactivate_existing' => false], $sent[1][0]->data());
        $this->assertSame('sent', $this->beehiiv($row)['status']);
    }

    public function test_retry_recorded_cannot_be_combined_with_since_or_all_time(): void
    {
        Http::fake();
        $this->retryFixtures();

        foreach ([['--since' => '2026-10-07'], ['--all-time' => true]] as $extra) {
            $this->assertSame(1, Artisan::call('beehiiv:backfill', ['--retry-recorded' => true, '--send' => true, '--delay' => 0] + $extra));
            $this->assertStringContainsString('--retry-recorded cannot be combined with --since or --all-time', Artisan::output());
        }

        $this->assertSame(1, Artisan::call('beehiiv:backfill', ['--retry-recorded' => true, '--since' => '2026-10-07']), 'also refused as a dry run');
        Http::assertNothingSent();
    }

    public function test_retry_recorded_skips_a_row_whose_email_was_sent_through_another_row(): void
    {
        Http::fake();
        $this->entry(['email' => 'same@example.com', 'fields' => json_encode(['beehiiv_status' => 'failed'])], false);
        $this->entry(['email' => 'SAME@example.com', 'form' => FormEntry::FORM_NEWSLETTER_HEADER, 'fields' => json_encode(['beehiiv_status' => 'sent'])], false);

        Artisan::call('beehiiv:backfill', ['--retry-recorded' => true]);

        $this->assertStringContainsString('Would send: 0 distinct emails', Artisan::output());
    }

    public function test_retry_recorded_stops_after_three_failures_and_keeps_the_delay_option(): void
    {
        Http::fake([self::URL => Http::response(['status' => 500], 500)]);
        foreach (['a', 'b', 'c', 'd', 'e'] as $name) {
            $this->entry(['email' => "$name@example.com", 'fields' => json_encode(['beehiiv_status' => 'failed'])], false);
        }

        $started = microtime(true);
        $this->assertSame(1, Artisan::call('beehiiv:backfill', ['--retry-recorded' => true, '--send' => true, '--delay' => 0.2]));
        $elapsed = microtime(true) - $started;

        Http::assertSentCount(3);
        $this->assertStringContainsString('Stopping after 3 failures in a row', Artisan::output());
        $this->assertGreaterThanOrEqual(0.3, $elapsed, 'two pauses of 0.2s happen between the first three requests');
    }

    public function test_retry_recorded_send_without_credentials_does_nothing(): void
    {
        config(['services.beehiiv.key' => '']);
        Http::fake();
        $this->retryFixtures();

        $this->assertSame(1, Artisan::call('beehiiv:backfill', ['--retry-recorded' => true, '--send' => true, '--delay' => 0]));

        Http::assertNothingSent();
    }

    /* ------------------------------------------------------------------ the fields column */

    public function test_fields_array_copes_with_a_string_a_double_encoded_string_an_array_and_junk(): void
    {
        $entry = new FormEntry;

        $entry->fields = json_encode(['a' => 1]);
        $this->assertSame(['a' => 1], $entry->fieldsArray());

        $entry->fields = json_encode(json_encode(['a' => 1]));
        $this->assertSame(['a' => 1], $entry->fieldsArray());

        $entry->fields = null;
        $this->assertSame([], $entry->fieldsArray());

        $entry->fields = 'not json at all';
        $this->assertSame([], $entry->fieldsArray());

        $cast = new FormEntry;
        $cast->mergeCasts(['fields' => 'array']);
        $cast->fields = ['a' => 1];
        $this->assertSame(['a' => 1], $cast->fieldsArray());
    }

    public function test_recording_works_when_the_fields_column_is_cast_to_an_array(): void
    {
        Http::fake([self::URL => Http::response(['data' => ['status' => 'active']], 200)]);

        $entry = ArrayCastFormEntry::withoutEvents(fn () => ArrayCastFormEntry::create([
            'form' => FormEntry::FORM_NEWSLETTER_SIDEBAR,
            'email' => 'cast@example.com',
            'url' => 'https://spannr.com/x',
            'fields' => ['email' => 'cast@example.com', 'keep' => 'me'],
        ]));

        SubscribeToBeehiiv::dispatchSync($entry);

        $stored = json_decode((string) DB::table('form_entries')->value('fields'), true);
        $this->assertSame('me', $stored['keep']);
        $this->assertSame('cast@example.com', $stored['email']);
        $this->assertSame('sent', $stored['beehiiv_status']);
    }

    public function test_all_four_admin_form_entry_screens_render_rows_that_carry_the_beehiiv_keys(): void
    {
        Http::fake([self::URL => Http::response(['data' => ['status' => 'active']], 200)]);

        Livewire::test(NewsletterOptInForm::class)->set('email', 'ann@example.com')->call('submit');

        $nested = ['beehiiv_status' => 'sent', 'beehiiv_http_status' => 200, 'beehiiv_subscription_status' => 'active', 'beehiiv_at' => '2026-10-07T10:00:00+00:00'];
        $this->entry(['form' => FormEntry::FORM_CONTACT, 'email' => 'contact@example.com', 'fields' => json_encode(['subject' => 'Hi', 'message' => 'Msg'] + $nested)], false);
        $this->entry(['form' => FormEntry::FORM_INVESTOR_OPTIN, 'email' => 'inv@example.com', 'fields' => json_encode(['accredited' => 'yes', 'phone' => '1'] + $nested)], false);
        $this->entry(['form' => FormEntry::FORM_FUNDING_APPLICANT, 'email' => 'fund@example.com', 'fields' => json_encode(['company_name' => 'Acme', 'phone' => '2'] + $nested)], false);

        Livewire::test(NewsletterSubscribers::class)->assertSee('ann@example.com');
        Livewire::test(WebsiteContactIndex::class)->assertSee('contact@example.com');
        Livewire::test(InvestorOptInIndex::class)->assertSee('inv@example.com');
        Livewire::test(FundingApplicantIndex::class)->assertSee('fund@example.com')->assertSee('Beehiiv status:'); // its "Show all fields" modal prints every key and value
    }

    public function test_the_beehiiv_keys_are_flat_so_nothing_that_echoes_every_value_can_break(): void
    {
        Http::fake([self::URL => Http::response(['data' => ['status' => 'active']], 200)]);
        $entry = $this->entry();

        $fields = json_decode((string) $entry->fresh()->fields, true);

        foreach ($fields as $key => $value) {
            $this->assertTrue(is_scalar($value) || $value === null, "fields.$key must not be an array or object");
        }
        $this->assertArrayNotHasKey('beehiiv', $fields);
        $this->assertSame('sent', $fields['beehiiv_status']);

        // Recording again replaces the old beehiiv_* keys. It never nests or leaves stale ones behind.
        config(['services.beehiiv.key' => '']);
        SubscribeToBeehiiv::dispatchSync($entry);

        $again = json_decode((string) $entry->fresh()->fields, true);
        $this->assertSame('skipped_no_credentials', $again['beehiiv_status']);
        $this->assertArrayNotHasKey('beehiiv_http_status', $again);
        $this->assertArrayNotHasKey('beehiiv_subscription_status', $again);
        $this->assertSame('visitor@example.com', $again['email']);
    }

    /* ------------------------------------------------------------------ the success message */

    public function test_the_success_message_is_one_exact_string_with_no_em_dash_en_dash_or_exclamation_mark(): void
    {
        $this->assertStringNotContainsString('!', self::SUCCESS_MESSAGE);
        $this->assertSame(0, preg_match('/[\x{2012}-\x{2015}]/u', self::SUCCESS_MESSAGE));

        $view = file_get_contents(resource_path('views/livewire/forms/newsletter-opt-in.blade.php'));
        $this->assertSame(1, substr_count($view, self::SUCCESS_MESSAGE), 'the message lives in the one shared view');
        $this->assertStringNotContainsString('Thanks for subscribing', $view);
    }
}

class ArrayCastFormEntry extends FormEntry
{
    protected $table = 'form_entries';

    protected $casts = ['fields' => 'array'];
}
