<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;
use Yadahan\AuthenticationLog\AuthenticationLogable;

class User extends Authenticatable implements MustVerifyEmail, HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity, AuthenticationLogable, InteractsWithMedia;

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'byline',
        'stripe_customer_id'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'stripe_customer_id'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(Activity::LOG_USERS)
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => $this->name . " was {$eventName}");
    }

    /**
     * Accessors
     */
    public function getCreatedDateAttribute(): string
    {
        return Carbon::parse($this->created_at)->format('Y-m-d');
    }

    public function getRoleColorAttribute($role): string
    {
        return match($role) {
            'Admin' => 'bg-primary',
            default => 'bg-info'
        };
    }

    public function getFullNameAttribute(): string
    {
        return $this->name . ($this->last_name ? ' ' . $this->last_name : '');
    }

    public function getTeamPhotoAttribute(): ?string
    {
        $media = $this->getFirstMedia('teamPhoto') ?? $this->getFirstMedia('profilePhoto');

        if (!$media) {
            return null;
        }

        return $media->hasGeneratedConversion('preview')
            ? $media->getUrl('preview')
            : $media->getUrl();
    }

    public function getShowUserPageAttribute(): bool
    {
        if ($this->articles->count() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getAboutPageUrlAttribute(): ?string
    {
        if ($this->articles->count() > 0) {
            return route('public.content.about.user', $this->slug);
        } else {
            return false;
        }
    }

    public function getShortBioAttribute(): string|null
    {
        return $this->bio ? Str::words($this->bio, 20) : null;
    }

    /**
     * Relationships
     */
    public function articles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    public function formEntries(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FormEntry::class);
    }

    public function jobs(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_managers');
    }

    public function jobSubscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JobSubscription::class);
    }

    public function listings(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Listing::class)->withPivot('role')->withTimestamps();
    }

    public function magicLinkTokens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MagicLinkToken::class);
    }

    public function marketplaceLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MarketplaceLog::class);
    }

    public function person(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Person::class);
    }

    public function setupIntents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SetupIntent::class);
    }

    public function subscription(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserSubscription::class);
    }

    /**
     * Newsletter / signup junk with no roles, unverified, and no domain data (safe to bulk-remove).
     *
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopeSpamCleanupCandidates($query)
    {
        return $query->whereDoesntHave('roles')
            ->whereNull('email_verified_at')
            ->whereDoesntHave('articles')
            ->whereDoesntHave('formEntries')
            ->whereDoesntHave('jobs')
            ->whereDoesntHave('jobSubscriptions')
            ->whereDoesntHave('listings')
            ->whereDoesntHave('magicLinkTokens')
            ->whereDoesntHave('marketplaceLogs')
            ->whereDoesntHave('person')
            ->whereDoesntHave('setupIntents')
            ->whereDoesntHave('subscription');
    }

    /**
     * Functions
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->crop(Manipulations::CROP_CENTER, 300, 300)
            ->nonQueued();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('teamPhoto')->singleFile();
        $this->addMediaCollection('profilePhoto')->singleFile();
    }

    public function isSubscriptionActive()
    {
        if ($this->subscription()->exists()) {
            if ($this->subscription->status === UserSubscription::STATUS_ACTIVE ||
                ($this->subscription->status === UserSubscription::STATUS_CANCELLED
                    && $this->subscription->end_date >= Carbon::today())) {
                return true;
            }
        }

        return false;
    }
}
