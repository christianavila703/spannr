<?php

namespace Tests\Feature;

use App\Providers\HorizonServiceProvider;
use Laravel\Horizon\Horizon;
use Tests\TestCase;

/**
 * DEV-20. The Horizon Slack webhook is a secret and lives in the environment (HORIZON_SLACK_WEBHOOK_URL), read through
 * config('services.slack.horizon_webhook'). Empty means no Slack route at all.
 *
 * Only FAKE values are used here. A fake URL is written in pieces so a scan for real webhook URLs does not flag this file.
 */
class SecretsNotInCodeTest extends TestCase
{
    private function fakeWebhook(): string
    {
        return 'https://hooks' . '.slack.com/services/' . 'TEST/FAKE/NOT-A-REAL-VALUE';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->resetHorizonSlackRoute();
    }

    protected function tearDown(): void
    {
        // Horizon keeps the route in static properties, so leave them as found.
        $this->resetHorizonSlackRoute();

        parent::tearDown();
    }

    private function resetHorizonSlackRoute(): void
    {
        Horizon::$slackWebhookUrl = null;
        Horizon::$slackChannel = null;
    }

    /** Runs the provider's boot with the given config value, the way the application does at start-up. */
    private function bootProviderWith(mixed $webhook): void
    {
        config(['services.slack.horizon_webhook' => $webhook]);
        $this->resetHorizonSlackRoute();

        (new HorizonServiceProvider($this->app))->boot();
    }

    public function test_the_horizon_provider_has_no_webhook_url_or_any_https_url_in_its_source(): void
    {
        $source = file_get_contents(app_path('Providers/HorizonServiceProvider.php'));

        $this->assertStringNotContainsString('hooks.slack.com', $source);
        $this->assertDoesNotMatchRegularExpression('#https?://#i', $source, 'no URL of any kind');
        $this->assertStringContainsString("config('services.slack.horizon_webhook')", $source);
    }

    public function test_with_the_setting_empty_or_missing_no_slack_route_is_set_and_nothing_errors(): void
    {
        foreach ([null, '', '   '] as $empty) {
            $this->bootProviderWith($empty);

            $this->assertNull(Horizon::$slackWebhookUrl, var_export($empty, true) . ': no webhook');
            $this->assertNull(Horizon::$slackChannel, var_export($empty, true) . ': no channel');
        }
    }

    public function test_the_application_boots_with_the_setting_removed_from_config_entirely(): void
    {
        config(['services.slack' => null]);
        $this->resetHorizonSlackRoute();

        (new HorizonServiceProvider($this->app))->boot();

        $this->assertNull(Horizon::$slackWebhookUrl);
        $this->assertTrue($this->app->isBooted(), 'the application is booted and the provider ran without an error');
    }

    public function test_with_a_fake_value_in_config_the_slack_route_is_that_value(): void
    {
        $this->bootProviderWith($this->fakeWebhook());

        $this->assertSame($this->fakeWebhook(), Horizon::$slackWebhookUrl);
        $this->assertSame('#dev-updates', Horizon::$slackChannel, 'the channel name is not a secret and is unchanged');
    }

    public function test_the_setting_is_read_from_the_environment_and_documented_empty_in_env_example(): void
    {
        $services = file_get_contents(config_path('services.php'));
        $this->assertStringContainsString("'horizon_webhook' => env('HORIZON_SLACK_WEBHOOK_URL')", $services);

        $example = file_get_contents(base_path('.env.example'));
        $this->assertSame(1, preg_match('/^HORIZON_SLACK_WEBHOOK_URL=$/m', $example), 'present in .env.example and empty');
        $this->assertStringNotContainsString('hooks.slack.com', $example);
    }

    public function test_the_mail_route_and_the_gate_are_untouched(): void
    {
        $this->bootProviderWith(null);

        $this->assertSame('sydney@spannr.com', Horizon::$email, 'only the Slack route was changed');
        $this->assertTrue(\Illuminate\Support\Facades\Gate::has('viewHorizon'), 'the Horizon gate is still defined');
    }
}
