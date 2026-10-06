<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Listing extends Model implements HasMedia
{
    use HasFactory,
        HasSEO,
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
     * SEO
     */
    public function getDynamicSEOData(): SEOData
    {
        $this->loadMissing('seo');

        $title = $this->cleanSeoText($this->seo?->title) ?: $this->seo_title;
        $description = $this->cleanSeoText($this->seo?->description) ?: $this->seo_description;

        $robots = strcasecmp((string) $this->status, self::STATUS_PENDING) === 0
            ? 'noindex, nofollow'
            : 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';

        return new SEOData(
            title: $title,
            description: $description,
            image: $this->featured_image_url ?: null,
            url: $this->show_url,
            enableTitleSuffix: false,
            modified_time: $this->updated_at,
            site_name: 'Spannr',
            locale: 'en_US',
            robots: $robots,
        );
    }

    public function getSeoTitleAttribute(): string
    {
        $type = Str::title($this->cleanSeoText($this->type));

        $label = match ($type) {
            self::TYPE_CLINIC => 'Longevity Clinic',
            self::TYPE_DOCTOR => 'Longevity Doctor',
            self::TYPE_RETREAT => 'Longevity Retreat',
            default => trim('Longevity ' . $type),
        };

        $location = $this->seoLocation();
        $title = $this->cleanSeoText($this->name) . ' - ' . $label . ($location ? ' in ' . $location : '');
        $titleWithSuffix = $title . ' | Spannr';

        return mb_strlen($titleWithSuffix) > 60 ? $title : $titleWithSuffix;
    }

    public function getSeoDescriptionAttribute(): string
    {
        $name = $this->cleanSeoText($this->name);
        $location = $this->seoLocation();
        $services = $this->seoServices();

        if ($location && $services) {
            $description = match (Str::title($this->cleanSeoText($this->type))) {
                self::TYPE_CLINIC => "{$name} in {$location} offers {$services}. See doctors, pricing model and how to book.",
                self::TYPE_DOCTOR => "{$name} is a longevity doctor in {$location} focused on {$services}. Read the profile and book a consult.",
                default => "{$name} in {$location} offers {$services}. See the profile, details and how to book.",
            };
        } else {
            $description = $this->cleanSeoText($this->summary)
                ?: $this->cleanSeoText($this->description)
                ?: "{$name} on the Spannr longevity marketplace. See the profile, details and how to book.";
        }

        return $this->limitSeoText($description, 155);
    }

    /**
     * "City, ST" for US addresses, "City, Country" otherwise. Null when either part is missing.
     */
    protected function seoLocation(): ?string
    {
        $address = $this->primaryAddress;

        if (! $address) {
            return null;
        }

        $city = $this->cleanSeoText($address->city);
        $isUs = in_array(strtolower(trim((string) $address->country_code)), ['us', 'usa', 'united states'], true);
        $region = $this->cleanSeoText($isUs ? $address->state_code : $address->country);

        return ($city && $region) ? "{$city}, {$region}" : null;
    }

    /**
     * Up to three treatment names as a sentence fragment: "A, B and C".
     */
    protected function seoServices(): ?string
    {
        $names = $this->treatments
            ->pluck('name')
            ->map(fn ($name) => $this->cleanSeoText($name))
            ->filter()
            ->unique()
            ->sort()
            ->take(3)
            ->values();

        if ($names->isEmpty()) {
            return null;
        }

        if ($names->count() === 1) {
            return $names->first();
        }

        return $names->slice(0, -1)->implode(', ') . ' and ' . $names->last();
    }

    /**
     * Plain text for titles and descriptions: no tags or entities, no em or en dashes
     * (including ones that come from stored data), single spaces.
     */
    protected function cleanSeoText(?string $text): string
    {
        $text = html_entity_decode(strip_tags((string) $text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+[\x{2012}-\x{2015}]\s+/u', ' - ', $text);
        $text = preg_replace('/[\x{2012}-\x{2015}]/u', '-', $text);

        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    protected function limitSeoText(string $text, int $limit): string
    {
        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        $cut = mb_substr($text, 0, $limit - 3);
        $lastSpace = mb_strrpos($cut, ' ');

        if ($lastSpace !== false) {
            $cut = mb_substr($cut, 0, $lastSpace);
        }

        return rtrim($cut, " ,;:-.") . '...';
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

    public function getUsersCountAttribute(): int
    {
        return $this->users->count() ?? 0;
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
