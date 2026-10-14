<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Spatie\SlackAlerts\Exceptions\WebhookUrlNotValid;
use Spatie\SlackAlerts\Facades\SlackAlert;
use Tests\TestCase;

/**
 * DEV-21. config/slack-alerts.php holds no webhook URL: every channel reads SLACK_ALERTS_WEBHOOK_URL_<NAME> if there is one,
 * otherwise the shared SLACK_ALERTS_WEBHOOK_URL.
 *
 * Only FAKE values are used (example.test). Http::fake means nothing is ever sent. The environment is controlled per test, so a
 * real value in a developer's .env cannot change the result, and it is put back afterwards.
 */
class SlackAlertsConfigTest extends TestCase
{
    private const CHANNELS = ['default', 'index', 'investing', 'jobs', 'website', 'test', 'marketplace'];

    private const SHARED = 'https://example.test/fake-webhook';

    /** @var array<string, array{env: mixed, server: mixed, put: string|false}> what each variable was before the test */
    private array $saved = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Start every test with none of the settings present, whatever this machine's .env holds.
        foreach ($this->variableNames() as $name) {
            $this->saved[$name] = [
                'env' => array_key_exists($name, $_ENV) ? $_ENV[$name] : null,
                'server' => array_key_exists($name, $_SERVER) ? $_SERVER[$name] : null,
                'put' => getenv($name),
            ];
            $this->unsetVariable($name);
        }

        Http::fake();
    }

    protected function tearDown(): void
    {
        foreach ($this->saved as $name => $before) {
            $this->unsetVariable($name);

            if ($before['env'] !== null) {
                $_ENV[$name] = $before['env'];
            }
            if ($before['server'] !== null) {
                $_SERVER[$name] = $before['server'];
            }
            if ($before['put'] !== false) {
                putenv($name . '=' . $before['put']);
            }
        }

        parent::tearDown();
    }

    /** @return array<int, string> */
    private function variableNames(): array
    {
        return array_merge(
            ['SLACK_ALERTS_WEBHOOK_URL'],
            array_map(fn (string $channel) => 'SLACK_ALERTS_WEBHOOK_URL_' . strtoupper($channel), self::CHANNELS),
        );
    }

    private function unsetVariable(string $name): void
    {
        unset($_ENV[$name], $_SERVER[$name]);
        putenv($name);
    }

    private function setVariable(string $name, string $value): void
    {
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
        putenv($name . '=' . $value);
    }

    /**
     * The webhook_urls the config file gives with the current environment, and the app's config set to them
     * (the file is evaluated again, the same thing that happens when the application boots).
     *
     * @return array<string, string|null>
     */
    private function loadUrls(): array
    {
        $config = require config_path('slack-alerts.php');
        config(['slack-alerts' => $config]);

        return $config['webhook_urls'];
    }

    /* ------------------------------------------------------------------ the file itself */

    public function test_the_config_file_has_no_hooks_slack_text_no_url_and_no_old_channel_names(): void
    {
        $source = file_get_contents(config_path('slack-alerts.php'));

        $this->assertStringNotContainsString('hooks.slack.com', $source);
        $this->assertDoesNotMatchRegularExpression('#https?://#i', $source, 'no URL literal of any kind');
        foreach (['#dev-updates', '#index-notifications', '#investing', '#jobs', '#website', '#test-notifications', '#marketplace'] as $oldChannel) {
            $this->assertStringNotContainsString($oldChannel, $source, 'the abandoned workspace channel names are gone');
        }
        $this->assertStringContainsString("env('SLACK_ALERTS_WEBHOOK_URL')", $source);
    }

    public function test_all_seven_channel_names_are_still_there_and_the_job_setting_is_unchanged(): void
    {
        $config = require config_path('slack-alerts.php');

        $this->assertSame(self::CHANNELS, array_keys($config['webhook_urls']));
        $this->assertSame(\Spatie\SlackAlerts\Jobs\SendToSlackChannelJob::class, $config['job']);
    }

    public function test_every_channel_the_app_asks_for_exists_in_the_config(): void
    {
        $used = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(app_path())) as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), '.php')) {
                preg_match_all("/SlackAlert::to\('([a-z_]+)'\)/", file_get_contents($file->getPathname()), $found);
                array_push($used, ...$found[1]);
            }
        }
        $used[] = \App\Models\UserSubscription::SLACK_NOTIFICATION_CHANNEL;

        $this->assertNotEmpty($used);
        $this->assertSame([], array_values(array_diff(array_unique($used), self::CHANNELS)), 'a channel the code uses has no config key');
    }

    public function test_env_example_documents_the_setting_empty(): void
    {
        $example = file_get_contents(base_path('.env.example'));

        $this->assertSame(1, preg_match('/^SLACK_ALERTS_WEBHOOK_URL=$/m', $example), 'present and empty');
        $this->assertStringNotContainsString('hooks.slack.com', $example);
    }

    /* ------------------------------------------------------------------ what each channel resolves to */

    public function test_with_the_shared_value_set_every_channel_resolves_to_it(): void
    {
        $this->setVariable('SLACK_ALERTS_WEBHOOK_URL', self::SHARED);

        $urls = $this->loadUrls();

        foreach (self::CHANNELS as $channel) {
            $this->assertSame(self::SHARED, $urls[$channel], $channel);
            $this->assertSame(self::SHARED, config('slack-alerts.webhook_urls.' . $channel), $channel . ' (through config())');
        }
    }

    public function test_a_per_channel_override_applies_to_that_channel_only(): void
    {
        $this->setVariable('SLACK_ALERTS_WEBHOOK_URL', self::SHARED);
        $this->setVariable('SLACK_ALERTS_WEBHOOK_URL_WEBSITE', 'https://example.test/fake-website');

        $urls = $this->loadUrls();

        $this->assertSame('https://example.test/fake-website', $urls['website']);
        foreach (array_diff(self::CHANNELS, ['website']) as $channel) {
            $this->assertSame(self::SHARED, $urls[$channel], $channel . ' keeps the shared value');
        }
    }

    public function test_an_override_alone_works_without_a_shared_value_and_leaves_the_rest_empty(): void
    {
        $this->setVariable('SLACK_ALERTS_WEBHOOK_URL_JOBS', 'https://example.test/fake-jobs');

        $urls = $this->loadUrls();

        $this->assertSame('https://example.test/fake-jobs', $urls['jobs']);
        foreach (array_diff(self::CHANNELS, ['jobs']) as $channel) {
            $this->assertNull($urls[$channel], $channel);
        }
    }

    public function test_an_empty_per_channel_line_falls_back_to_the_shared_value(): void
    {
        $this->setVariable('SLACK_ALERTS_WEBHOOK_URL', self::SHARED);
        $this->setVariable('SLACK_ALERTS_WEBHOOK_URL_TEST', '');

        $this->assertSame(self::SHARED, $this->loadUrls()['test'], 'a blank line in .env does not switch the channel off');
    }

    public function test_with_nothing_set_every_channel_is_null(): void
    {
        foreach ($this->loadUrls() as $channel => $url) {
            $this->assertNull($url, $channel);
        }
    }

    /* ------------------------------------------------------------------ sending */

    public function test_with_nothing_set_sending_an_alert_does_nothing_and_never_throws(): void
    {
        $this->loadUrls();

        foreach (self::CHANNELS as $channel) {
            SlackAlert::to($channel)->message('A fake alert for ' . $channel);
            SlackAlert::to($channel)->blocks([['type' => 'section', 'text' => ['type' => 'mrkdwn', 'text' => 'fake']]]);
        }
        SlackAlert::message('A fake alert on the default channel');

        Http::assertNothingSent();
        $this->assertTrue(true, 'no exception was thrown');
    }

    public function test_with_the_shared_value_set_an_alert_is_sent_to_it_and_an_override_goes_to_its_own_address(): void
    {
        $this->setVariable('SLACK_ALERTS_WEBHOOK_URL', self::SHARED);
        $this->setVariable('SLACK_ALERTS_WEBHOOK_URL_MARKETPLACE', 'https://example.test/fake-marketplace');
        $this->loadUrls();

        SlackAlert::to('website')->message('fake one');
        SlackAlert::to('marketplace')->message('fake two');

        Http::assertSentCount(2);
        Http::assertSent(fn ($request) => $request->url() === self::SHARED && $request['text'] === 'fake one');
        Http::assertSent(fn ($request) => $request->url() === 'https://example.test/fake-marketplace' && $request['text'] === 'fake two');
    }

    public function test_a_malformed_value_is_the_one_case_the_package_throws_on_so_it_must_stay_a_real_url(): void
    {
        // Documents what the package does (vendor/spatie/laravel-slack-alerts Config::getWebhookUrl): empty is skipped, malformed throws.
        $this->setVariable('SLACK_ALERTS_WEBHOOK_URL', 'not a url');
        $this->loadUrls();

        $this->expectException(WebhookUrlNotValid::class);

        SlackAlert::to('website')->message('fake');
    }
}
