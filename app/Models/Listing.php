<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Listing extends Model implements HasMedia
{
    use HasFactory,
        InteractsWithMedia;

    protected $guarded = ['id'];

    protected $casts = [
        'licenses' => 'array',
        'certifications' => 'array',
        'payment_options' => 'array',
        'emails' => 'array'
    ];

    const STATUS_PENDING = 'Pending';
    const STATUS_PUBLIC = 'Public';

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PUBLIC,
    ];

    const TYPE_CLINIC = 'Clinic';
    const TYPE_DOCTOR = 'Doctor';
    const TYPE_COACH = 'Coach';
    const TYPE_RETREAT = 'Retreat';

    const TYPES = [
        self::TYPE_CLINIC,
        self::TYPE_DOCTOR,
        self::TYPE_COACH,
        self::TYPE_RETREAT
    ];

    const USER_ROLE_OWNER = 'Owner';
    const USER_ROLE_MANAGER = 'Manager';

    const USER_ROLES = [
        self::USER_ROLE_OWNER,
        self::USER_ROLE_MANAGER
    ];

    const SOFT_LAUNCH_COLD_PLUNGE = 'Cold Plunge';
    const SOFT_LAUNCH_RED_LIGHT_THERAPY = 'Red Light Therapy';
    const SOFT_LAUNCH_SAUNA = 'Sauna';

    // TODO: Temporary for Marketplace soft launch
    const SOFT_LAUNCH_TREATMENT_OPTIONS = [
        [
            'name' => self::SOFT_LAUNCH_COLD_PLUNGE,
            'slug' => 'cold-plunge'
        ],
        [
            'name' => self::SOFT_LAUNCH_RED_LIGHT_THERAPY,
            'slug' => 'red-light-therapy'
        ],
        [
            'name' => self::SOFT_LAUNCH_SAUNA,
            'slug' => 'sauna'
        ]
    ];

    // TODO: Add SearchableEntity trait

    /**
     * Relationships
     */
    public function addresses(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function ageGroups(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable')->whereHas('parent', function (Builder $query) {
            $query->where('name', Tag::TAG_AGE_GROUPS);
        });
    }

    public function connectedListings(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Listing::class, 'listable', 'listing_relationships')->withPivot('label')->withTimestamps();
    }

    public function formEntries(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(FormEntry::class, 'entity');
    }

    public function languages(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable')->whereHas('parent', function (Builder $query) {
            $query->where('name', Tag::TAG_LANGUAGES);
        });
    }

    public function managers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class)->wherePivot('role', self::USER_ROLE_MANAGER)->withTimestamps();
    }

    public function listingClicks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MarketplaceLog::class)->where('action', MarketplaceLog::TYPE_LISTING_CLICK_FROM_INDEX);
    }

    public function marketplaceLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MarketplaceLog::class);
    }

    public function organizations(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Organization::class, 'listable', 'listing_relationships')->withPivot('label')->withTimestamps();
    }

    public function owners(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class)->wherePivot('role', self::USER_ROLE_OWNER)->withTimestamps();
    }

    public function people(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Person::class, 'listable', 'listing_relationships')->withPivot('label')->withTimestamps();
    }

    public function primaryAddress(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Address::class, 'addressable')->where('primary', 1);
    }

    public function otherAddresses(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Address::class, 'addressable')->where('primary', 0);
    }

    public function relatable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function treatments(): \Illuminate\Database\Eloquent\Relations\MorphToMany|Builder
    {
        return $this->morphToMany(Tag::class, 'taggable')->whereHas('tagSets', function (Builder $query) {
            $query->where('name', TagSet::TAG_SET_MEDICAL_TREATMENTS);
        })->withTimestamps();
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function websiteVisits(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MarketplaceLog::class)->where('action', MarketplaceLog::TYPE_WEBSITE_VISIT);
    }

    /**
     * Scopes
     */
    public function scopePublic($query)
    {
        return $query->where('status', self::STATUS_PUBLIC);
    }

    public function scopeDistance($query, $from_latitude, $from_longitude, $distance)
    {
        $raw = DB::raw('ROUND ( ( 3959 * acos( cos( radians(' . $from_latitude . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $from_longitude . ') ) + sin( radians(' . $from_latitude . ') ) * sin( radians( latitude ) ) ) ) , 2) AS distance');
        return $query->select('*')->addSelect($raw)->orderBy('distance', 'ASC')->having('distance', '<=', $distance);
    }

    /**
     * Mutators
     */
    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::of($value)->slug('-');
    }

    /**
     * Attributes
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('featured') ?? NULL;
    }

    public function getMediumFeaturedImageUrlAttribute(): ?string
    {
        if ($this->getFirstMedia('featured')) {
            return $this->getFirstMedia('featured')->getUrl('medium');
        } else {
            return NULL;
        }
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            self::TYPE_CLINIC => 'fa-sharp fa-solid fa-bed-pulse',
            self::TYPE_DOCTOR => 'fa-sharp fa-solid fa-user-doctor-message',
            self::TYPE_RETREAT => 'fa-sharp fa-solid fa-tree-city',
            self::TYPE_COACH => 'fa-sharp fa-solid fa-user-headset',
            default => 'fa-sharp fa-regular fa-cart-shopping-fast'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-warning-neon text-dark',
            self::STATUS_PUBLIC => 'bg-success',
            default => 'bg-danger'
        };
    }

    public function getPrettyCreatedDateAttribute(): string
    {
        return Carbon::parse($this->created_at)->format('Y-m-d');
    }

    public function getPrettyUpdatedDateAttribute(): string
    {
        return Carbon::parse($this->updated_at)->format('Y-m-d');
    }

    public function getShowUrlAttribute(): string
    {
        return strtolower(route('public.marketplace.show', [$this->type, $this->slug]));
    }

    /**
     * Functions
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->keepOriginalImageFormat()
            ->crop(Manipulations::CROP_CENTER, 300, 225)
            ->nonQueued();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('medium')
                    ->keepOriginalImageFormat()
                    ->width(680)
                    ->height(540)
                    ->crop(Manipulations::CROP_CENTER, 680, 510)
                    ->nonQueued();
        });
    }
}
