<?php

namespace App\Models;

use App\Http\Traits\HasImage;
use App\Http\Traits\HasSocialMedia;
use App\Http\Traits\SearchableEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Organization extends Model
{
    use HasFactory,
        HasSocialMedia,
        LogsActivity,
        SearchableEntity,
        HasImage;

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

    private string $searchableRouteName = 'organizations.show';
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

    public function stock(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Stock::class);
    }

    // People Relationships
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
}
