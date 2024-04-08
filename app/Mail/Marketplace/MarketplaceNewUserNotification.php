<?php

namespace App\Mail\Marketplace;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MarketplaceNewUserNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Listing $listing;
    public User $user;
    public bool $newUser;

    public function __construct(Listing $listing, User $user, bool $newUser)
    {
        $this->listing = $listing;
        $this->user = $user;
        $this->newUser = $newUser;
    }

    public function envelope()
    {
        return new Envelope(
            subject: 'Spannr Marketplace - Manage Your Listing',
        );
    }

    public function content()
    {
        return new Content(
            markdown: 'emails.marketplace.new-user-notification',
            with: [
                'url' => $this->listing->show_url
            ]
        );
    }

    public function attachments()
    {
        return [];
    }
}
