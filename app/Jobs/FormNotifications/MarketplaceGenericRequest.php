<?php

namespace App\Jobs\FormNotifications;

use App\Models\FormEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\SlackAlerts\Facades\SlackAlert;

class MarketplaceGenericRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected FormEntry $formEntry;
    protected array $formFields;

    public function __construct(FormEntry $formEntry)
    {
        $this->formEntry = $formEntry;
        $this->formFields = json_decode($formEntry->fields, true);
    }

    public function handle()
    {
        $this->sendToSlack();
    }

    private function sendToSlack() {

        $message = '<!here> *Marketplace Generic Request* ' . "\n" .
            '>' . $this->formEntry->first_name . ' ' . $this->formEntry->last_name . "\n" .
            '>' . $this->formEntry->email . "\n";

        $message .= "```" . $this->formFields['message'] . "```";

        SlackAlert::to('test')->message($message);
    }
}
