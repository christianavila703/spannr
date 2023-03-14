<?php

namespace App\Jobs\FormNotifications;

use App\Helpers\SettingsHelper;
use App\Mail\InvestorOptInCreated;
use App\Models\FormEntry;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use MailchimpMarketing;
use Spatie\SlackAlerts\Facades\SlackAlert;

class InvestorOptIn implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected FormEntry $formEntry;

    protected array $formFields;

    protected $onlyMailchimp;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(FormEntry $formEntry, $onlyMailchimp = false)
    {
        $this->formEntry = $formEntry;
        $this->formFields = json_decode($formEntry->fields, true);
        $this->onlyMailchimp = $onlyMailchimp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->sendToMailChimp();

        if ($this->onlyMailchimp === false) {
            $this->sendToEmail();

            if ($this->formFields['accredited'] === FormEntry::INVESTOR_ACCREDITED_YES) {
                $this->sendToSlackIfAccredited();
            }
        }
    }

    private function sendToSlackIfAccredited()
    {
        $message = '*Syndicate Application*' . "\n" .
            ">" . $this->formEntry->first_name . ' ' . $this->formEntry->last_name . "\n" .
            ">" . $this->formEntry->email . "\n" .
            ">" . $this->formFields['phone'];

        SlackAlert::to('investing')->message($message);
    }

    private function sendToMailChimp()
    {
        $formEntry = $this->formEntry;
        $formFields = $this->formFields;

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

    private function sendToEmail()
    {
        $mail = new InvestorOptInCreated($this->formEntry, $this->formFields);
        $recipientEmails = SettingsHelper::getEmailsArrayFromConfig('mail.form_emails.investor');

        foreach ($recipientEmails as $recipient) {
            Mail::to($recipient)->send($mail);
        }
    }
}
