<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\SlackAlerts\Facades\SlackAlert;

class FormEntry extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const FORM_INVESTOR_OPTIN = 'Investor Opt-in';
    const FORM_CONTACT = 'Contact';

    protected static function booted()
    {
        static::created(function ($formEntry) {

            $fields = json_decode($formEntry->fields, true);

            if ($formEntry->form === self::FORM_CONTACT) {
                $message = '<!here> *Website Contact* ' . "\n" . '>' . $formEntry->first_name . ' ' . $formEntry->last_name . "\n" . '>' . $formEntry->email . "\n\n" .
                    "```" . $fields['message'] . "```";
                SlackAlert::to('website')->message($message);
            }

            if ($formEntry->form === self::FORM_INVESTOR_OPTIN) {
                if ($fields['accredited'] === 'yes') {
                    $message = '*Syndicate Application*' . "\n" .
                        ">" . $formEntry->first_name . ' ' . $formEntry->last_name . "\n" .
                        ">" . $formEntry->email . "\n" .
                        ">" . $fields['phone'];
                    SlackAlert::to('investing')->message($message);
                }
            }
        });
    }
}
