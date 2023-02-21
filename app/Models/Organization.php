<?php

namespace App\Models;

use App\Helpers\Entity\FieldsMapping;
use App\Http\Traits\HasBreadcrumbs;
use App\Http\Traits\HasImage;
use App\Http\Traits\HasSocialMedia;
use App\Http\Traits\SearchableEntity;
use App\Models\Contracts\EntityContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Organization extends Model implements EntityContract
{
    use HasFactory,
        HasSocialMedia,
        LogsActivity,
        SearchableEntity,
        HasImage,
        HasBreadcrumbs;

    protected $guarded = ['id'];

    const TYPE_PRIVATE = 'Private';
    const TYPE_PUBLIC = 'Public';
    const TYPE_NONPROFIT = 'Non-Profit';
    const TYPE_GOVERNMENT = 'Government';
    const TYPE_EDUCATIONAL = 'Educational';
    const TYPE_MEDICAL = 'Medical';
    const TYPE_DAO = 'DAO';

    const TYPES = [
        self::TYPE_PRIVATE,
        self::TYPE_PUBLIC,
        self::TYPE_NONPROFIT,
        self::TYPE_GOVERNMENT,
        self::TYPE_EDUCATIONAL,
        self::TYPE_MEDICAL,
        self::TYPE_DAO
    ];

    private string $searchableRouteName = 'admin.organizations.show';
    private string $searchableRouteField = 'slug';
    private string $searchableIcon = 'fa-duotone fa-buildings';

    private string $modelName = 'organizations';
    private string $imageUrlStoragePath = 'public/images/organizations';

    /**
     * Functions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(Activity::LOG_ORGANIZATIONS)
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => $this->name . " was {$eventName}");
    }

    /**
     * Relationships
     */
    public function fundraisingRounds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FundraisingRound::class)->orderByDesc('date');
    }

    public function investors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Investor::class)->withTimestamps();
    }

    public function jobs(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Job::class, 'owner');
    }

    public function locations(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Location::class, 'locatable' , 'location_relationships')->withTimestamps();
    }

    public function mediaItems(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(MediaItem::class)->withTimestamps();
    }

    public function stock(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Stock::class);
    }

    public function tags(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function people(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Person::class)->withPivot('role')->withTimestamps();
    }

    /**
     * Mutators
     */
    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::of($value)->slug('-');
    }

    public function getSeoDescriptionAttribute(): string
    {
        return Str::words($this->description, 25);
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
            'extended_description' => [
                'type' => FieldsMapping::TYPE_TEXT,
            ],
            'founded_date' => [
                'type' => FieldsMapping::TYPE_DATE,
                'format' => 'Y'
            ],
            'facebook' => [
                'type' => FieldsMapping::TYPE_STRING,
            ],
            'twitter' => [
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
            'fundraisingRounds' => [
                'type' => FieldsMapping::TYPE_RELATION,
                'relation' => FieldsMapping::RELATION_ONE_N,
                'relationField' => 'name',
            ],
            'investors' => [
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
            'mediaItems' => [
                'type' => FieldsMapping::TYPE_RELATION,
                'relation' => FieldsMapping::RELATION_N_N,
                'relationField' => 'name',
            ],
            'stock' => [
                'type' => FieldsMapping::TYPE_RELATION,
                'relation' => FieldsMapping::RELATION_ONE_N,
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
