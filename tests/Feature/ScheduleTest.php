<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

/**
 * DEV-19. The server had no "schedule:run" cron line for a long time, so nothing in app/Console/Kernel.php ran. Adding the
 * line starts every active scheduled command at once, so the list is trimmed to the safe and wanted ones. This test pins
 * that list: a command added back (or one lost) has to be a deliberate change.
 *
 * It only reads the schedule. It never runs a scheduled command.
 */
class ScheduleTest extends TestCase
{
    /** @return array<int, Event> */
    private function events(): array
    {
        // The console kernel is bootstrapped with the application, which defines the schedule.
        return app(Schedule::class)->events();
    }

    /** The artisan command and its arguments, without the PHP binary and the "artisan" word: "beehiiv:backfill --send". */
    private function artisanCommand(Event $event): string
    {
        $command = str_replace(["'", '"'], '', (string) $event->command);

        return trim(substr($command, strpos($command, 'artisan') + strlen('artisan')));
    }

    /** @return array<int, string> */
    private function scheduled(): array
    {
        return array_map(fn (Event $event) => $this->artisanCommand($event), $this->events());
    }

    private function event(string $command): ?Event
    {
        foreach ($this->events() as $event) {
            if ($this->artisanCommand($event) === $command) {
                return $event;
            }
        }

        return null;
    }

    public function test_exactly_the_kept_commands_are_scheduled(): void
    {
        $scheduled = $this->scheduled();
        sort($scheduled);

        $this->assertSame([
            'beehiiv:backfill --send --retry-recorded',
            'magic-link-tokens:clear',
            'record-locks:clear',
        ], $scheduled);
    }

    public function test_each_disabled_command_is_not_scheduled(): void
    {
        $scheduled = implode("\n", $this->scheduled());

        foreach ([
            'backup:run' => 'covered by the server mysqldump to Google Drive',
            'backup:clean' => 'covered by the server mysqldump to Google Drive',
            'horizon:snapshot' => 'Horizon is not in use',
            'form-entry:resubmit-mailchimp' => 'Mailchimp is retired',
            'subscription:check' => 'needs a decision, off until then',
            'subscriptions:check-jobs' => 'the jobs boards are retired',
        ] as $command => $why) {
            $this->assertStringNotContainsString($command, $scheduled, $command . ' (' . $why . ')');
            $this->assertNull($this->event($command), $command);
        }
    }

    public function test_the_beehiiv_retry_runs_hourly_with_its_flags_and_cannot_overlap(): void
    {
        $event = $this->event('beehiiv:backfill --send --retry-recorded');

        $this->assertNotNull($event, 'scheduled with both flags');
        $this->assertSame('0 * * * *', $event->expression, 'hourly, on the hour');
        $this->assertTrue($event->withoutOverlapping, 'a slow run cannot be started twice');
        $this->assertSame(120, $event->expiresAt, 'the overlap lock expires after 2 hours');
    }

    public function test_the_beehiiv_retry_never_runs_in_a_mode_that_touches_old_mailchimp_rows(): void
    {
        $command = $this->event('beehiiv:backfill --send --retry-recorded')->command;

        $this->assertStringContainsString('--retry-recorded', $command);
        $this->assertStringNotContainsString('--all-time', $command);
        $this->assertStringNotContainsString('--since', $command);
    }

    public function test_the_two_cleanup_commands_keep_their_schedule(): void
    {
        $this->assertSame('30 2 * * *', $this->event('magic-link-tokens:clear')->expression, 'daily at 02:30');
        $this->assertSame('* * * * *', $this->event('record-locks:clear')->expression, 'every minute');
    }

    public function test_every_scheduled_command_is_a_registered_artisan_command(): void
    {
        $registered = array_keys(\Illuminate\Support\Facades\Artisan::all());

        foreach ($this->scheduled() as $scheduled) {
            $this->assertContains(explode(' ', $scheduled)[0], $registered, $scheduled);
        }
    }

    public function test_the_disabled_lines_are_kept_in_the_kernel_with_a_reason_so_they_can_be_switched_back_on(): void
    {
        $kernel = file_get_contents(app_path('Console/Kernel.php'));

        foreach (['backup:clean', 'backup:run', 'horizon:snapshot', 'form-entry:resubmit-mailchimp', 'subscription:check', 'subscriptions:check-jobs'] as $command) {
            $this->assertStringContainsString("// \$schedule->command('" . $command . "')", $kernel, $command . ' is commented out, not deleted');
        }
        $this->assertSame(5, substr_count($kernel, 'DEV-19 disabled'), 'one note per disabled item (the two backup lines share one)');
        $this->assertStringContainsString('To re-enable', $kernel);
    }
}
