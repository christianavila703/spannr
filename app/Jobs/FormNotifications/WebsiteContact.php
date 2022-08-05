<?php

namespace App\Jobs\FormNotifications;

use App\Models\FormEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\SlackAlerts\Facades\SlackAlert;

class WebsiteContact implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected FormEntry $formEntry;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(FormEntry $formEntry)
    {
        $this->formEntry = $formEntry;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $fields = json_decode($this->formEntry->fields, true);
        $message = '<!here> *Website Contact* ' . "\n" . '>' . $this->formEntry->first_name . ' ' . $this->formEntry->last_name . "\n" . '>' . $this->formEntry->email . "\n\n" .
                    "```" . $fields['message'] . "```";
                SlackAlert::to('website')->message($message);
    }
}
