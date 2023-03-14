<?php

namespace App\Jobs\FormNotifications;

use App\Models\FormEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResubmitMailchimpApplicableForms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mailchimpApplicableForms = FormEntry::MAILCHIMP_APPLICABLE_FORMS;
        $formEntries = FormEntry::whereIn('form', array_keys($mailchimpApplicableForms))
            ->whereNull('mailchimp_status')
            ->get();

        foreach ($formEntries as $formEntry) {
            $jobClass = $mailchimpApplicableForms[$formEntry->form];

            if ($jobClass === InvestorOptIn::class) {
                InvestorOptIn::dispatch($formEntry, true);
            } elseif ($formEntry->form === FormEntry::FORM_REPORT_LONGEVITY_2022) {
                $interestId = config('mailchimp.interest_ids.longevity_report_2022');

                $jobClass::dispatch($formEntry, $interestId);
            } else {
                $jobClass::dispatch($formEntry);
            }
        }
    }
}
