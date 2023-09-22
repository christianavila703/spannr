<?php

namespace App\Models;

use App\Helpers\Entity\FieldsMapping;
use App\Http\Traits\HasBreadcrumbs;
use App\Http\Traits\HasImage;
use App\Http\Traits\HasSocialMedia;
use App\Http\Traits\SearchableEntity;
use App\Models\Contracts\EntityContract;
use App\Models\Traits\Lockable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Investor extends Model implements EntityContract
{
    use HasFactory,
        HasSocialMedia,
        LogsActivity,
        SearchableEntity,
        HasImage,
        HasBreadcrumbs,
        Lockable;

    protected $guarded = ['id'];

    const TYPE_ANGEL = 'Angel Investor';
    const TYPE_FUND = 'Fund';
    const TYPE_CORPORATE = 'Corporate';

    const TYPES = [
        'Academic Fund',
        self::TYPE_ANGEL,
        'Asset Management',
        self::TYPE_CORPORATE,
        'Family Office',
        'Foundation',
        self::TYPE_FUND,
        'Hedge Fund',
        'Incubator',
        'Platform'
    ];

    const ICON_CSS_CLASS = 'fa-duotone fa-hands-holding-dollar';

    private string $searchableRouteName = 'admin.investors.show';
    private string $searchableRouteField = 'slug';
    private string $searchableIcon = self::ICON_CSS_CLASS;

    private string $modelName = 'investors';
    private string $imageUrlStoragePath = 'public/images/investors';

    /**
     * Relationships
     */
    public function fundraisingRounds(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(FundraisingRound::class);
    }

    public function jobs(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Job::class, 'owner');
    }

    public function locations(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Location::class, 'locatable' , 'location_relationships')->withTimestamps();
    }

    public function organizations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Organization::class);
    }

    public function tags(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function events(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Event::class, 'relatable', 'event_relationships');
    }

    // Owner - Organization or Person Record
    public function ownable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    // People Relationships
    public function founders(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Person::class)->where('role', Person::ORGANIZATION_ROLE_FOUNDER)->withTimestamps();
    }

    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Person::class)->where('role', '!=', Person::ORGANIZATION_ROLE_FOUNDER)->withTimestamps();
    }

    public function people(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Person::class)->orderBy('name')->withPivot('role')->withTimestamps();
    }

    /**
     * Attributes
     */
    //TODO check ownable attributes usage and remove if unnecessary
    public function getOwnableNameAttribute(): string
    {
        if ($this->ownable_id) {
            return $this->ownable->name;
        } else {
            return '';
        }
    }

    public function getOwnableAdminLinkAttribute(): string
    {
        if ($this->ownable_id) {
            return $this->ownable->admin_link;
        } else {
            return '';
        }
    }

    public function getOwnableIconAttribute(): string
    {
        if ($this->ownable_id) {
            return $this->ownable->investor_icon;
        } else {
            return '';
        }
    }

    public function getOwnablePrettyTypeAttribute(): ?string
    {
        if ($this->ownable_type) {
            $array = explode('\\', $this->ownable_type);
            return array_pop($array);
        } else {
            return '';
        }
    }

    /**
     * Mutators
     */
    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::of($value)->slug('-');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(Activity::LOG_INVESTORS)
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => $this->name . " was {$eventName}");
    }

    /**
     * @return array
     */
    public static function getFieldsMapping()
    {
        return [
            //attributes
            'name' => [
                'type' => FieldsMapping::TYPE_STRING,
                'required' => true,
            ],
            'slug' => [
                'type' => FieldsMapping::TYPE_STRING,
            ],
            'type' => [
                'type' => FieldsMapping::TYPE_ENUM,
                'values' => self::TYPES,
                'required' => true,
            ],
            'website' => [
                'type' => FieldsMapping::TYPE_STRING,
            ],
            'summary' => [
                'type' => FieldsMapping::TYPE_STRING,
            ],
            'description' => [
                'type' => FieldsMapping::TYPE_TEXT,
            ],
            'facebook' => [
                'type' => FieldsMapping::TYPE_STRING,
            ],
            'instagram' => [
                'type' => FieldsMapping::TYPE_STRING,
            ],
            'linkedin' => [
                'type' => FieldsMapping::TYPE_STRING,
            ],
            'image' => [
                'type' => FieldsMapping::TYPE_IMAGE,
            ],
            //relations
            'fundraising_rounds' => [
                'type' => FieldsMapping::TYPE_RELATION,
                'relation' => FieldsMapping::RELATION_N_N,
                'relationField' => 'name',
            ],
            'jobs' => [
                'type' => FieldsMapping::TYPE_RELATION,
                'relation' => FieldsMapping::RELATION_ONE_N_MORPHABLE,
                'relationField' => 'name',
            ],
            'locations' => [
                'type' => FieldsMapping::TYPE_RELATION,
                'relation' => FieldsMapping::RELATION_N_N_MORPHABLE,
                'relationField' => 'name',
            ],
            'organizations' => [
                'type' => FieldsMapping::TYPE_RELATION,
                'relation' => FieldsMapping::RELATION_N_N,
                'relationField' => 'name',
            ],
            'tags' => [
                'type' => FieldsMapping::TYPE_RELATION,
                'relation' => FieldsMapping::RELATION_N_N_MORPHABLE,
                'relationField' => 'name',
            ],
            'people' => [
                'type' => FieldsMapping::TYPE_RELATION,
                'relation' => FieldsMapping::RELATION_N_N,
                'relationField' => 'name',
                'pivotColumns' => [
                    'role',
                ],
            ],
        ];
    }
}
