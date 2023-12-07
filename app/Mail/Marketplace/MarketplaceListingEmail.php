<?php

namespace App\Mail\Marketplace;

use App\Models\FormEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MarketplaceListingEmail extends Mailable
{
    use Queueable, SerializesModels;

    public FormEntry $formEntry;
    public array $formFields;

    public function __construct(FormEntry $formEntry, array $formFields)
    {
        $this->formEntry = $formEntry;
        $this->formFields = $formFields;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Spannr Marketplace Email from ' . $this->formEntry->first_name . ' ' . $this->formEntry->last_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.marketplace.listing-email',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
