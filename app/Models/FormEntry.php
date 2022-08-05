<?php

namespace App\Models;

use App\Jobs\FormNotifications\InvestorOptIn;
use App\Jobs\FormNotifications\WebsiteContact;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormEntry extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const FORM_INVESTOR_OPTIN = 'Investor Opt-in';
    const FORM_CONTACT = 'Contact';

    const STATUS_MAILCHIMP_SUCCESS = 'Successful';
    const STATUS_MAILCHIMP_FAILED = 'Failed';

    const INVESTOR_ACCREDITED_YES = 'Yes';
    const INVESTOR_ACCREDITED_NO = 'No';
    const INVESTOR_ACCREDITED_UNSURE = 'Unsure';

    const INVESTOR_ACCREDITED_FIELDS = [
        self::INVESTOR_ACCREDITED_YES,
        self::INVESTOR_ACCREDITED_NO,
        self::INVESTOR_ACCREDITED_UNSURE,
    ];

    protected static function booted()
    {
        static::created(function ($formEntry) {
            if ($formEntry->form === self::FORM_CONTACT) {
                WebsiteContact::dispatch($formEntry);
            }

            if ($formEntry->form === self::FORM_INVESTOR_OPTIN) {
                InvestorOptIn::dispatch($formEntry);
            }
        });
    }

    public function scopeWebsiteContact(Builder $query): Builder
    {
        return $query->where('form', self::FORM_CONTACT);
    }

    public function scopeInvestorOptin(Builder $query): Builder
    {
        return $query->where('form', self::FORM_INVESTOR_OPTIN);
    }

    public function getNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFormattedDateAttribute(): string
    {
        return Carbon::parse($this->created_at)->format('M d, Y g:i a');
    }

    public function getEmailLinkAttribute(): string
    {
        return '<a href="mailto:' . $this->email . '">' . $this->email . '</a>';
    }

    public function getFormattedMessageFieldAttribute() {
        $fields = json_decode($this->fields);
        return $fields->message;
    }

    public function getFormattedAccreditedFieldAttribute() {
        $fields = json_decode($this->fields);
        if (property_exists($fields, 'accredited')) {
            return $fields->accredited;
        }
    }

    public function getFormattedMailChimpResponseAttribute() {
        return print_r(json_decode($this->mailchimp_response), true);
    }
}
