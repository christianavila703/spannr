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

class Person extends Model implements EntityContract
{
    use HasFactory,
        HasSocialMedia,
        LogsActivity,
        SearchableEntity,
        HasImage,
        HasBreadcrumbs;

    const ORGANIZATION_ROLE_FOUNDER = 'Founder';
    const ORGANIZATION_ROLE_TEAM = 'Team';

    const ORGANIZATION_ROLES = [
        self::ORGANIZATION_ROLE_FOUNDER,
        self::ORGANIZATION_ROLE_TEAM
    ];

    protected $guarded = ['id'];

    private string $searchableRouteName = 'admin.people.show';
    private string $searchableRouteField = 'slug';
    private string $searchableIcon = 'fa-duotone fa-user-hair-buns';

    private string $modelName = 'people';
    private string $imageUrlStoragePath = 'public/images/people';

    /**
     * Scopes
     */
    public function scopeFounders($query) {
        return $query->where('role', self::ORGANIZATION_ROLE_FOUNDER);
    }

    public function scopeTeam($query) {
        return $query->where('role', self::ORGANIZATION_ROLE_TEAM);
    }

    /**
     * Mutators
     */
    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::of($value)->slug('-');
    }

    /**
     * Relationships
     */
    public function investors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Investor::class)->withPivot('role')->withTimestamps();
    }

    public function locations(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Location::class, 'locatable' , 'location_relationships')->withTimestamps();
    }

    public function organizations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Organization::class)->withPivot('role')->withTimestamps();
    }

    public function tags(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Functions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(Activity::LOG_PEOPLE)
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
            'byline' => [
                'type' => FieldsMapping::TYPE_STRING,
            ],
            'job_type' => [
                'type' => FieldsMapping::TYPE_STRING,
            ],
            'website' => [
                'type' => FieldsMapping::TYPE_STRING,
            ],
            'bio' => [
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
            'google_scholar' => [
                'type' => FieldsMapping::TYPE_STRING,
            ],
            'image' => [
                'type' => FieldsMapping::TYPE_IMAGE,
            ],
            //relations
            'investors' => [
                'type' => FieldsMapping::TYPE_RELATION,
                'relation' => FieldsMapping::RELATION_N_N,
                'relationField' => 'name',
            ],
            'organizations' => [
                'type' => FieldsMapping::TYPE_RELATION,
                'relation' => FieldsMapping::RELATION_N_N,
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
