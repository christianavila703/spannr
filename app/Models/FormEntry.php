<?php

namespace App\Models;

use App\Jobs\FormNotifications\FundingApplicant;
use App\Jobs\FormNotifications\InvestorOptIn;
use App\Jobs\FormNotifications\JobPostingSubmitted;
use App\Jobs\FormNotifications\ListingEmail;
use App\Jobs\FormNotifications\MarketplaceGenericRequest;
use App\Jobs\FormNotifications\ReportOptIn;
use App\Jobs\FormNotifications\SubscribeToBeehiiv;
use App\Jobs\FormNotifications\WebsiteContact;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Throwable;

class FormEntry extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // TODO: This should be a cast but I think it will break things so fix it later
//    protected $casts = [
//        'fields' => 'array'
//    ];

    const FORM_INVESTOR_OPTIN = 'Investor Opt-in';
    const FORM_FUNDING_APPLICANT = 'Funding Applicant';
    const FORM_CONTACT = 'Contact';

    const FORM_NEWSLETTER_SIDEBAR = 'Newsletter Sidebar';
    const FORM_NEWSLETTER_STICKY = 'Newsletter Sticky';
    const FORM_NEWSLETTER_HOMEPAGE_HERO = 'Newsletter Homepage Hero';
    const FORM_NEWSLETTER_HEADER = 'Newsletter Header';
    const FORM_NEWSLETTER_FOOTER = 'Newsletter Footer';
    const FORM_NEWSLETTER_ABOUT = 'Newsletter About';

    const FORM_REPORT_LONGEVITY_2022 = 'Longevity Report 2022';
    const FORM_JOB_POSTING = 'Job Posting';

    const FORM_MARKETPLACE_LISTING_EMAIL = 'Marketplace Listing Email';
    const FORM_MARKETPLACE_GENERIC_REQUEST = 'Marketplace Generic Request';

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

    // Newsletter signups now go to beehiiv (see SubscribeToBeehiiv), so the newsletter forms are
    // deliberately NOT listed here: the scheduled daily Mailchimp resubmit must not pick them up.
    const MAILCHIMP_APPLICABLE_FORMS = [
        self::FORM_INVESTOR_OPTIN => InvestorOptIn::class,
        self::FORM_FUNDING_APPLICANT => FundingApplicant::class,
        self::FORM_REPORT_LONGEVITY_2022 => ReportOptIn::class,
    ];

    const NEWSLETTER_FORMS = [
        self::FORM_NEWSLETTER_SIDEBAR,
        self::FORM_NEWSLETTER_STICKY,
        self::FORM_NEWSLETTER_HOMEPAGE_HERO,
        self::FORM_NEWSLETTER_HEADER,
        self::FORM_NEWSLETTER_FOOTER,
        self::FORM_NEWSLETTER_ABOUT,
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
                case self::FORM_NEWSLETTER_STICKY:
                case self::FORM_NEWSLETTER_HOMEPAGE_HERO:
                case self::FORM_NEWSLETTER_HEADER:
                case self::FORM_NEWSLETTER_FOOTER:
                case self::FORM_NEWSLETTER_ABOUT:
                    // Production and staging run QUEUE_CONNECTION=sync (no worker, no Horizon), so a normal
                    // dispatch would make the visitor wait for beehiiv. Run it after the response has been
                    // sent instead, and never let a problem here reach the visitor.
                    try {
                        SubscribeToBeehiiv::dispatchAfterResponse($formEntry);
                    } catch (Throwable $exception) {
                        Log::warning('FormEntry ' . $formEntry->id . ': beehiiv dispatch failed (' . class_basename($exception) . ')');
                    }
                    break;

                case self::FORM_REPORT_LONGEVITY_2022:
                    $interestId = config('mailchimp.interest_ids.longevity_report_2022');

                    ReportOptIn::dispatch($formEntry, $interestId);
                    break;

                case self::FORM_JOB_POSTING:
                    JobPostingSubmitted::dispatch($formEntry);
                    break;

                case self::FORM_MARKETPLACE_LISTING_EMAIL:
                    ListingEmail::dispatch($formEntry);
                    break;

                case self::FORM_MARKETPLACE_GENERIC_REQUEST:
                    MarketplaceGenericRequest::dispatch($formEntry);
                    break;
            }
        });
    }

    /**
     * Relationships
     */
    public function entity(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
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
        return $query->whereIn('form', [self::FORM_NEWSLETTER_SIDEBAR, self::FORM_NEWSLETTER_HOMEPAGE_HERO]);
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

    /**
     * The fields column as an array. It is not cast today, so it arrives as a JSON string
     * (every job json_decodes it). This also copes with a JSON string inside a JSON string,
     * and with an array if a cast is ever switched on.
     */
    public function fieldsArray(): array
    {
        $fields = $this->fields;

        for ($pass = 0; $pass < 2 && is_string($fields); $pass++) {
            $fields = json_decode($fields, true);
        }

        return is_array($fields) ? $fields : [];
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
