<?php

namespace Tests\Feature;

use App\Http\Livewire\Public\Marketplace\ClaimListing;
use App\Http\Livewire\Public\Marketplace\ManageListing\Languages;
use App\Jobs\Subscriptions\UpdateExpiredSubscription;
use App\Models\Listing;
use App\Models\Role;
use App\Models\Tag;
use App\Models\UserSubscription;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\Feature\Concerns\BuildsPublicPageTables;
use Tests\TestCase;

/**
 * DEV-22. The old "at sydney" Slack user mention is gone from every alert message, and the alerts still send.
 *
 * In-memory SQLite and Http::fake: nothing is ever sent to Slack, and the Stripe client fails locally (empty key) before
 * any request. Only fake webhook addresses (example.test) are used.
 */
class SlackMentionsTest extends TestCase
{
    use BuildsPublicPageTables;

    /** The mention that was removed, written in pieces so this file does not contain it. */
    private function oldMention(): string
    {
        return '<@' . 'sydney>';
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Fail fast rather than ever creating a table in a real database.
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        $this->createTables();

        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable();
        });
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
        // Creating a user writes an activity log row.
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject', 'subject');
            $table->string('event')->nullable();
            $table->nullableMorphs('causer', 'causer');
            $table->json('properties')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->timestamps();
        });
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('stripe_subscription_id')->nullable();
            $table->integer('status')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });

        // Fake webhook addresses, and no real search engine or HTTP call.
        config([
            'scout.driver' => 'null',
            'slack-alerts.webhook_urls' => [
                'default' => 'https://example.test/fake-default',
                'index' => 'https://example.test/fake-index',
                'investing' => 'https://example.test/fake-investing',
                'jobs' => 'https://example.test/fake-jobs',
                'website' => 'https://example.test/fake-website',
                'test' => 'https://example.test/fake-test',
                'marketplace' => 'https://example.test/fake-marketplace',
            ],
        ]);
        Http::fake();
    }

    /** @return array<int, array{url: string, text: string}> every alert sent to the fake addresses, in order */
    private function sentAlerts(): array
    {
        $alerts = [];

        foreach (Http::recorded() as [$request]) {
            if (str_starts_with($request->url(), 'https://example.test/fake-')) {
                $alerts[] = ['url' => $request->url(), 'text' => (string) ($request['text'] ?? '')];
            }
        }

        return $alerts;
    }

    private function listing(string $name = 'Alpha Longevity Clinic', ?string $website = null): Listing
    {
        return Listing::factory()->create(['name' => $name, 'website' => $website]);
    }

    private function makeRole(string $name): void
    {
        DB::table('roles')->insert(['name' => $name, 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()]);
    }

    private function assertAlert(array $alert, string $url, array $contains): void
    {
        $this->assertSame($url, $alert['url']);
        $this->assertStringNotContainsString('<@', $alert['text'], 'no mention of any kind');
        $this->assertStringNotContainsString($this->oldMention(), $alert['text']);
        foreach ($contains as $fragment) {
            $this->assertStringContainsString($fragment, $alert['text']);
        }
    }

    /* ------------------------------------------------------------------ the files */

    public function test_no_php_file_under_app_config_routes_database_or_resources_has_the_old_mention(): void
    {
        $found = [];

        foreach (['app', 'config', 'routes', 'database', 'resources'] as $directory) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(base_path($directory))) as $file) {
                if ($file->isFile() && str_ends_with($file->getFilename(), '.php')
                    && stripos(file_get_contents($file->getPathname()), $this->oldMention()) !== false) {
                    $found[] = str_replace(base_path() . '/', '', $file->getPathname());
                }
            }
        }

        $this->assertSame([], $found);
    }

    /* ------------------------------------------------------------------ call sites run for real */

    public function test_a_manual_claim_verification_alert_still_sends_without_the_mention(): void
    {
        $this->makeRole(Role::ROLE_MARKETPLACE_MANAGER);
        $listing = $this->listing('Alpha Longevity Clinic');

        Livewire::test(ClaimListing::class, ['listing' => $listing])
            ->set('first_name', 'Pat')->set('last_name', 'Owner')->set('email', 'pat@owner.example.test')
            ->call('save')
            ->assertSet('manualClaimSuccess', true);

        $alerts = $this->sentAlerts();
        $this->assertCount(1, $alerts);
        $this->assertAlert($alerts[0], 'https://example.test/fake-marketplace', [
            'Manual Marketplace Listing Claim Verification',
            'Alpha Longevity Clinic',
            'Name: Pat Owner',
            'Email: pat@owner.example.test',
        ]);
        $this->assertStringStartsWith('Manual Marketplace Listing Claim Verification', $alerts[0]['text'], 'the message now starts with its own words');
    }

    public function test_the_claim_exception_alerts_still_send_without_the_mention(): void
    {
        // The role does not exist, so assigning it fails and each branch reports it.
        $manual = $this->listing('Manual Clinic');
        Livewire::test(ClaimListing::class, ['listing' => $manual])
            ->set('first_name', 'Pat')->set('last_name', 'Owner')->set('email', 'pat@owner.example.test')
            ->call('save')->assertSet('error', true);

        $matched = $this->listing('Matched Clinic', 'https://www.matched.test');
        Livewire::test(ClaimListing::class, ['listing' => $matched])
            ->set('first_name', 'Sam')->set('last_name', 'Owner')->set('email', 'sam@matched.test')
            ->call('save')->assertSet('error', true);

        $alerts = $this->sentAlerts();
        $this->assertCount(2, $alerts);
        $this->assertAlert($alerts[0], 'https://example.test/fake-marketplace', ['Exception during Claim Listing for manual verification #', 'Manual Clinic', 'pat@owner.example.test']);
        $this->assertStringStartsWith('Exception during Claim Listing for manual verification', $alerts[0]['text']);
        $this->assertAlert($alerts[1], 'https://example.test/fake-marketplace', ['Exception during Claim Listing with successful domain match for #', 'Matched Clinic', 'sam@matched.test']);
        $this->assertStringStartsWith('Exception during Claim Listing with successful domain match', $alerts[1]['text']);
    }

    public function test_the_new_language_tag_alert_still_sends_without_the_mention(): void
    {
        DB::table('tags')->insert(['name' => Tag::TAG_LANGUAGES, 'slug' => 'languages', 'depth' => 0, 'created_at' => now(), 'updated_at' => now()]);
        $listing = $this->listing('Alpha Longevity Clinic');

        Livewire::test(Languages::class, ['listing' => $listing])
            ->set('newItem', 'Klingon')
            ->call('createItem');

        $alerts = $this->sentAlerts();
        $this->assertCount(1, $alerts);
        $this->assertAlert($alerts[0], 'https://example.test/fake-test', ['*New Language Tag*', 'Listing Type: Clinic', 'Language Tag: Klingon', '/marketplace/clinic/alpha-longevity-clinic']);
        $this->assertStringStartsWith('*New Language Tag*', $alerts[0]['text'], 'the bold heading is now the first thing in the message');
    }

    public function test_the_expired_subscription_error_alert_still_sends_without_the_mention(): void
    {
        // An empty Stripe key makes the client fail locally, before any request, so the job reports the error to Slack.
        config(['services.stripe.api_key' => '']);
        $subscription = (new UserSubscription())->forceFill(['id' => 1, 'stripe_subscription_id' => 'sub_fake_123']);

        (new UpdateExpiredSubscription($subscription))->handle();

        $alerts = $this->sentAlerts();
        $this->assertCount(1, $alerts);
        $this->assertAlert($alerts[0], 'https://example.test/fake-website', ['*UpdateExpiredSubscription job error.*', 'Subscription Stripe ID: sub_fake_123', 'Message: ']);
        $this->assertStringStartsWith('*UpdateExpiredSubscription job error.*', $alerts[0]['text']);
    }

    /* ------------------------------------------------------------------ the other call sites, checked in the source */

    /**
     * Call sites that are hard to run in a test (they need Stripe, Polygon, a queue batch or a full checkout). For each: the
     * file still sends its alerts (same number of SlackAlert calls as before), still has its descriptive text, has no mention,
     * and no message is left as a bare fragment.
     *
     * @return array<string, array{0: string, 1: int, 2: array<int, string>}>
     */
    public static function sourceChecked(): array
    {
        return [
            'SubscriptionController' => ['app/Http/Controllers/Panel/SubscriptionController.php', 4, ['*Spannr+ subscription cancellation error*', '*Spannr+ subscription resuming error*', 'Subscription Stripe ID: ']],
            'CheckoutController' => ['app/Http/Controllers/Public/CheckoutController.php', 4, ['*Error during checkout for Job Posting subscription*', '*Error during checkout for Spannr+ subscription*']],
            'JobsController' => ['app/Http/Controllers/Users/JobsController.php', 2, ['*Error during cancellation Job Posting subscription*', 'Exception during Update Monthly Job Card for Job ']],
            'Apply' => ['app/Http/Livewire/Public/Jobs/Apply.php', 2, ["SlackAlert::message('Error during job application for '"]],
            'ClaimListing' => ['app/Http/Livewire/Public/Marketplace/ClaimListing.php', 3, ['Exception during Claim Listing with successful domain match for #', 'Manual Marketplace Listing Claim Verification', 'Exception during Claim Listing for manual verification #']],
            'Addresses' => ['app/Http/Livewire/Public/Marketplace/ManageListing/Addresses.php', 3, ['*Exception during saveLocation on Marketplace*', '*Exception during selectLocation on Marketplace*']],
            'Languages' => ['app/Http/Livewire/Public/Marketplace/ManageListing/Languages.php', 2, ['*New Language Tag*', '*Problem Adding Language*']],
            'MedicalTreatments' => ['app/Http/Livewire/Public/Marketplace/ManageListing/MedicalTreatments.php', 2, ['*Existing Tag Assigned to Treatments*', '*New Tag Assigned to Treatments*']],
            'Users' => ['app/Http/Livewire/Public/Marketplace/ManageListing/Users.php', 1, ['Exception during Marketplace Manage Users']],
            'GetEndofDayStockSnapshot' => ['app/Jobs/Polygon/GetEndofDayStockSnapshot.php', 2, ['Failure during EndOfDaySnapshot OpenClose ', 'Failure during EndOfDaySnapshot Tickers ', "SlackAlert::to('index')->message(\$message)"]],
            'GetMarketSnapshot' => ['app/Jobs/Polygon/GetMarketSnapshot.php', 4, ['Market Snapshot Batch Failure', 'Failure during GetMarketSnapshot', 'Missing tickers from hourly snapshot:', "SlackAlert::to('index')->message(\$message)"]],
            'ProcessSetupIntent' => ['app/Jobs/StripeWebhooks/ProcessSetupIntent.php', 1, ['Exception during ProcessSetupIntent for Job Subscription Webhook ']],
            'CheckJobSubscriptionStatus' => ['app/Jobs/Subscriptions/CheckJobSubscriptionStatus.php', 1, ['Job Subscription is no longer active ']],
            'UpdateExpiredSubscription' => ['app/Jobs/Subscriptions/UpdateExpiredSubscription.php', 1, ["'*' . \$errorTitle . '*'"]],
            'StartEndOfDayMarketSnapshot' => ['app/Console/Commands/StartEndOfDayMarketSnapshot.php', 1, ["message('EOD Snapshot ' . route('admin.longevity-index.all'))"]],
        ];
    }

    /** @dataProvider sourceChecked */
    public function test_the_other_call_sites_keep_their_alerts_and_wording_without_the_mention(string $path, int $alertCalls, array $fragments): void
    {
        $source = file_get_contents(base_path($path));

        $this->assertStringNotContainsString('<@', $source, $path . ': no mention of any kind');
        $this->assertSame($alertCalls, substr_count($source, 'SlackAlert::'), $path . ': every alert call is still there');
        foreach ($fragments as $fragment) {
            $this->assertStringContainsString($fragment, $source, $path . ': ' . $fragment);
        }
        $this->assertDoesNotMatchRegularExpression('/message\(\s*["\']\s*["\']\s*\)/', $source, 'no empty message');
    }

    public function test_the_channel_wide_here_pings_are_left_as_they_were(): void
    {
        foreach (['WebsiteContact', 'MarketplaceGenericRequest', 'ListingEmail'] as $job) {
            $this->assertStringContainsString("'<!here> *", file_get_contents(app_path('Jobs/FormNotifications/' . $job . '.php')), $job . ' keeps its channel ping (not part of this change)');
        }
    }
}
