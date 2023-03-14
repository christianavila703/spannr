<?php

namespace App\Console\Commands;

use App\Jobs\FormNotifications\ResubmitMailchimpApplicableForms;
use Illuminate\Console\Command;

class FormEntryResubmitMailchimp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'form-entry:resubmit-mailchimp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-submit Form Entries with empty Mailchimp status.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ResubmitMailchimpApplicableForms::dispatch();

        return 0;
    }
}
