<?php

namespace App\Notifications;

use App\Models\MagicLinkToken;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class SendLoginLink extends Notification
{
    /**
     * @var string
     */
    public string $token;

    /**
     * @param \App\Models\MagicLinkToken $magicLinkToken
     */
    public function __construct(MagicLinkToken $magicLinkToken)
    {
        $this->token = $magicLinkToken->token;
    }
    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $loginLinkUrl = route('login.link.token', $this->token);

        return (new MailMessage)
            ->subject(Lang::get('Login via link'))
            ->line(Lang::get('Please click the button below to login.'))
            ->action(Lang::get('Login'), $loginLinkUrl)
            ->line(Lang::get('If you did not request a link to login, no further action is required.'));
    }
}
