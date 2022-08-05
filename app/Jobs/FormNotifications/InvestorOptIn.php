<?php

namespace App\Jobs\FormNotifications;

use App\Models\FormEntry;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MailchimpMarketing;
use Spatie\SlackAlerts\Facades\SlackAlert;

class InvestorOptIn implements ShouldQueue
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
        $formFields = json_decode($this->formEntry->fields, true);
        $this->sendToSlackIfAccredited($this->formEntry, $formFields);
        $this->sendToMailChimp($this->formEntry, $formFields);
    }

    private function sendToSlackIfAccredited($formEntry, $formFields) {
        if ($formFields['accredited'] === 'yes') {
            $message = '*Syndicate Application*' . "\n" .
                ">" . $formEntry->first_name . ' ' . $formEntry->last_name . "\n" .
                ">" . $formEntry->email . "\n" .
                ">" . $formFields['phone'];
            SlackAlert::to('investing')->message($message);
        }
    }

    private function sendToMailChimp($formEntry, $formFields) {

        $subscribeArray = [
            "email_address" => $formEntry->email,
            "status" => "subscribed",
            "tags" => ["Investors"],
            "merge_fields" => [
                "FNAME" => $formEntry->first_name,
                "LNAME" => $formEntry->last_name,
                "PHONE" => $formFields['phone'],
                "ACC_STATUS" => ucwords($formFields['accredited'])
            ]
        ];

        $mailchimp = new MailchimpMarketing\ApiClient();

        $mailchimp->setConfig([
          'apiKey' => config('services.mailchimp.key'),
          'server' => config('services.mailchimp.server')
        ]);

        try {
            $response = $mailchimp->lists->addListMember("1dc3f748f8", $subscribeArray);
            $formEntry->mailchimp_response = json_encode($response);
            $formEntry->mailchimp_status = FormEntry::STATUS_MAILCHIMP_SUCCESS;
            $formEntry->save();
        } catch (ClientException $exception) {
            $errorMessage = $exception->getResponse()->getBody()->getContents();
            $formEntry->mailchimp_response = $errorMessage;
            $formEntry->mailchimp_status = FormEntry::STATUS_MAILCHIMP_FAILED;
            $formEntry->save();
        }
    }
}
