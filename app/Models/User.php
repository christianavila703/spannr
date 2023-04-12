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
        'byline'
    ];

    protected $hidden = [
        'password',
        'remember_token',
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
        if ($this->getFirstMedia('teamPhoto')) {
            return $this->getFirstMedia('teamPhoto')->getUrl('preview');
        } else {
            return NULL;
        }
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
    public function articles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function person(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Person::class);
    }

    /**
     * Relationships
     */
    public function magicLinkTokens(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MagicLinkToken::class);
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
}
