<?php

namespace App\Jobs\FormNotifications;

use App\Helpers\SettingsHelper;
use App\Mail\WebsiteContactCreated;
use App\Models\FormEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Spatie\SlackAlerts\Facades\SlackAlert;

class WebsiteContact implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected FormEntry $formEntry;

    protected array $formFields;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(FormEntry $formEntry)
    {
        $this->formEntry = $formEntry;
        $this->formFields = json_decode($formEntry->fields, true);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sendToEmail();
        $this->sendToSlack();
    }

    private function sendToSlack() {

        $message = '<!here> *Website Contact* ' . "\n" . '>' . $this->formEntry->first_name . ' ' .
            $this->formEntry->last_name . "\n" . '>' . $this->formEntry->email . "\n\n" . "```" .
            $this->formFields['message'] . "```";

        SlackAlert::to('website')->message($message);
    }

    private function sendToEmail()
    {
        $mail = new WebsiteContactCreated($this->formEntry, $this->formFields);
        $recipientEmails = SettingsHelper::getEmailsArrayFromConfig('mail.form_emails.general');

        foreach ($recipientEmails as $recipient) {
            Mail::to($recipient)->send($mail);
        }
    }
}
