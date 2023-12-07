<?php

namespace App\Jobs\FormNotifications;

use App\Mail\Marketplace\MarketplaceListingEmail;
use App\Models\FormEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Spatie\SlackAlerts\Facades\SlackAlert;

class ListingEmail implements ShouldQueue {
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
        $this->sendToEmail();
        $this->sendToSlack();
    }

    private function sendToSlack() {

        $message = '<!here> *Marketplace Listing Email* ' . "\n" .
            '>' . $this->formEntry->first_name . ' ' . $this->formEntry->last_name . "\n" .
            '>' . $this->formEntry->email . "\n";

        if (array_key_exists('phone', $this->formFields)) {
            $message .= '>' . $this->formFields['phone'] . "\n";
        }

        $message .= '>' . $this->formEntry->entity->name . "\n";
        $message .= '>' . $this->formEntry->entity->show_url . "\n";

        $message .= "```" . $this->formFields['message'] . "```";

        SlackAlert::to('test')->message($message);
    }

    private function sendToEmail()
    {
        $mail = new MarketplaceListingEmail($this->formEntry, $this->formFields);
        $recipientEmails = $this->formEntry->entity->emails;

        foreach ($recipientEmails as $recipient) {
            Mail::to($recipient)->send($mail);
        }
    }

}
