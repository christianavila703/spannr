<?php

namespace App\Models;

use App\Jobs\FormNotifications\FundingApplicant;
use App\Jobs\FormNotifications\InvestorOptIn;
use App\Jobs\FormNotifications\NewsletterOptIn;
use App\Jobs\FormNotifications\ReportOptIn;
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
    const FORM_FUNDING_APPLICANT = 'Funding Applicant';
    const FORM_CONTACT = 'Contact';
    const FORM_NEWSLETTER_SIDEBAR = 'Newsletter Sidebar';
    const FORM_NEWSLETTER_STICKY = 'Newsletter Sticky';
    const FORM_NEWSLETTER_HOMEPAGE_HERO = 'Newsletter Homepage Hero';
    const FORM_NEWSLETTER_FOOTER = 'Newsletter Footer';
    const FORM_NEWSLETTER_ABOUT = 'Newsletter About';
    const FORM_REPORT_LONGEVITY_2022 = 'Longevity Report 2022';

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
            switch ($formEntry->form) {
                case self::FORM_CONTACT:
                    WebsiteContact::dispatch($formEntry);
                    break;

                case self::FORM_INVESTOR_OPTIN:
                    InvestorOptIn::dispatch($formEntry);
                    break;

                case self::FORM_FUNDING_APPLICANT:
                    FundingApplicant::dispatch($formEntry);
                    break;

                case self::FORM_NEWSLETTER_SIDEBAR:
                    NewsletterOptIn::dispatch($formEntry);
                    break;

                case self::FORM_REPORT_LONGEVITY_2022:
                    ReportOptIn::dispatch($formEntry, "23e01b7e78");
                    break;
            }
        });
    }

    /**
     * Scopes
     */
    public function scopeWebsiteContact(Builder $query): Builder
    {
        return $query->where('form', self::FORM_CONTACT);
    }

    public function scopeInvestorOptin(Builder $query): Builder
    {
        return $query->where('form', self::FORM_INVESTOR_OPTIN);
    }

    public function scopeFundingApplicant(Builder $query): Builder
    {
        return $query->where('form', self::FORM_FUNDING_APPLICANT);
    }

    public function scopeNewsletterOptin(Builder $query): Builder
    {
        return $query->whereIn('form', [self::FORM_NEWSLETTER_SIDEBAR, self::FORM_NEWSLETTER_STICKY]);
    }

    /**
     * Accessors
     */
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
        return $this->fields['message'] ?? '';
    }

    public function getFormattedSubjectFieldAttribute() {
        return $this->fields['subject'] ?? '';
    }

    public function getFormattedAccreditedFieldAttribute() {
        return $this->fields['accredited'] ?? '';
    }

    public function getFormattedMailChimpResponseAttribute(): bool|string
    {
        return print_r(json_decode($this->mailchimp_response), true);
    }
}
