<?php

namespace App\Models;

use App\Helpers\Entity\FieldsMapping;
use App\Http\Traits\HasBreadcrumbs;
use App\Http\Traits\HasRichText;
use App\Http\Traits\SearchableEntity;
use App\Models\Contracts\EntityContract;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Throwable;

class Job extends Model implements EntityContract
{
    use HasFactory,
        LogsActivity,
        SearchableEntity,
        HasRichText,
        HasBreadcrumbs;

    protected $guarded = ['id'];

    const STATUS_OPEN = 'Open';
    const STATUS_ARCHIVED = 'Archived';

    const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_ARCHIVED
    ];

    const TYPE_FULL_TIME = 'Full Time';
    const TYPE_PART_TIME = 'Part Time';
    const TYPE_CONTRACT = 'Contract';

    const TYPES = [
        self::TYPE_FULL_TIME,
        self::TYPE_PART_TIME,
        self::TYPE_CONTRACT
    ];

    private string $searchableRouteName = 'admin.jobs.show';
    private string $searchableRouteField = 'slug';
    private string $searchableIcon = 'fa-duotone fa-briefcase';

    private string $modelName = 'jobs';
    private string $imageUrlStoragePath = 'public/images/jobs';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(Activity::LOG_JOBS)
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => $this->name . " was {$eventName}");
    }

    /**
     * Relationships
     */
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function locations(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Location::class, 'locatable' , 'location_relationships')->withTimestamps();
    }

    public function owner(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function tags(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Accessors
     */
    public function getPrettyPostedDateAttribute(): string
    {
        return Carbon::parse($this->posted_date)->format('M j, Y');
    }

    public function getPrettyExpDateAttribute(): string
    {
        return Carbon::parse($this->exp_date)->format('M j, Y');
    }

    public function getOwnerShowLinkAttribute(): string
    {
        $ownerRoute = match($this->owner_type) {
            Organization::class => 'admin.organizations.show',
            Investor::class => 'admin.investors.show'
        };

        try {
            return route($ownerRoute, $this->owner->slug);
        } catch(Throwable $throwable) {
            Log::critical('URL Problem with Job' . $throwable->getMessage());
            return '';
        }
    }

    public function getUrlLinkAttribute(): string
    {
        return '<a href="' . $this->url . '" target="_blank" rel="noopener noreferrer">' . $this->url . '</a>';
    }

    /**
     * Mutators
     */
    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::of($value)->slug('-');
    }

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
            'status' => [
                'type' => FieldsMapping::TYPE_ENUM,
                'values' => self::STATUSES,
                'required' => true,
            ],
            'description' => [
                'type' => FieldsMapping::TYPE_TEXT,
            ],
            'posted_date' => [
                'type' => FieldsMapping::TYPE_DATE,
                'required' => true,
            ],
            'exp_date' => [
                'type' => FieldsMapping::TYPE_DATE,
            ],
            'salary' => [
                'type' => FieldsMapping::TYPE_INTEGER,
            ],
            'hourly' => [
                'type' => FieldsMapping::TYPE_FLOAT,
            ],
            'url' => [
                'type' => FieldsMapping::TYPE_STRING,
            ],
            'remote' => [
                'type' => FieldsMapping::TYPE_BOOLEAN,
            ],
            //relations
            'category' => [
                'type' => FieldsMapping::TYPE_RELATION,
                'relation' => FieldsMapping::RELATION_ONE_ONE,
                'relationField' => 'name',
            ],
            'owner'    => [
                'type'          => FieldsMapping::TYPE_RELATION,
                'relation'      => FieldsMapping::RELATION_ONE_ONE_MORPHABLE,
                'relationMorphableTypes' => [
                    Organization::class,
                    Investor::class,
                ],
                'morphableFieldId' => 'owner_id',
                'morphableFieldType' => 'owner_type',
                'relationField' => 'name',
            ],
            'locations' => [
                'type' => FieldsMapping::TYPE_RELATION,
                'relation' => FieldsMapping::RELATION_N_N_MORPHABLE,
                'relationField' => 'name',
            ],
            'tags' => [
                'type' => FieldsMapping::TYPE_RELATION,
                'relation' => FieldsMapping::RELATION_N_N_MORPHABLE,
                'relationField' => 'name',
            ],
        ];
    }
}
