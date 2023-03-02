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

class FundingApplicant implements ShouldQueue
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
        $this->sendToMailChimp();
    }

    private function sendToMailChimp()
    {
        $formEntry = $this->formEntry;
        $formFields = $this->formFields;

        $subscribeArray = [
            "email_address" => $formEntry->email,
            "status" => "subscribed",
            "tags" => ["Funding Applicant"],
            "merge_fields" => [
                "FNAME" => $formEntry->first_name,
                "LNAME" => $formEntry->last_name,
                "PHONE" => $formFields['phone'],
                "COMPANY" => $formFields['company_name']
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
