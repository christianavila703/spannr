<?php

namespace App\Jobs\FormNotifications;

use App\Models\FormEntry;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MailchimpMarketing;

class ReportOptIn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected FormEntry $formEntry;
    protected array $formFields;
    protected string $interestId;

    /**
     * Note: You need to get the interest ID for the group from MailChimp API
     *
     *  example to get LeadGen sub-groups
     *  $response = $mailchimp->lists->listInterestCategoryInterests(
            "1dc3f748f8",
            "6580f3d8a8"
        );
     */

    public function __construct($formEntry, $interestId)
    {
        $this->formEntry = $formEntry;
        $this->formFields = json_decode($formEntry->fields, true);
        $this->interestId = $interestId;
    }

    public function handle()
    {
        $this->sendToMailChimp();
    }

    private function sendToMailChimp()
    {
        $formEntry = $this->formEntry;
        $formFields = $this->formFields;

        $subscribeArray = [
            "email_address" => $formEntry->email,
            "status" => "subscribed",
            "ip_signup" => $this->formFields['ip'],
            "ip_opt" => $this->formFields['ip'],
            "interests" => [
                $this->interestId => true
            ]
        ];

        $mailchimp = new MailchimpMarketing\ApiClient();
        $mailchimp->setConfig([
            'apiKey' => config('services.mailchimp.key'),
            'server' => config('services.mailchimp.server')
        ]);

        try {
            $response = $mailchimp->lists->setListMember("1dc3f748f8", $formEntry->email, $subscribeArray);
            $formEntry->mailchimp_response = json_encode($response);
            $formEntry->mailchimp_status = FormEntry::STATUS_MAILCHIMP_SUCCESS;
        } catch (ClientException $exception) {
            $errorMessage = $exception->getResponse()->getBody()->getContents();
            $formEntry->mailchimp_response = $errorMessage;
            $formEntry->mailchimp_status = FormEntry::STATUS_MAILCHIMP_FAILED;
        }

        $formEntry->save();
    }
}
