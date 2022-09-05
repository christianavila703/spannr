<?php

namespace App\Mail;

use App\Models\FormEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvestorOptInCreated extends Mailable
{
    use Queueable, SerializesModels;

    public FormEntry $formEntry;

    public array $formFields;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(FormEntry $formEntry, array $formFields)
    {
        $this->formEntry = $formEntry;
        $this->formFields = $formFields;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Syndicate Application')
            ->view('emails.form.investor-opt-in');
    }
}
