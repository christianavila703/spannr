<?php

namespace App\Models;

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
        SearchableEntity;

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

    /**
     * Functions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('organizations')
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

    // People Relationships
    public function founders(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Person::class)->where('role', Person::ORGANIZATION_ROLE_FOUNDER)->withTimestamps();
    }

    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Person::class)->where('role', Person::ORGANIZATION_ROLE_TEAM)->withTimestamps();
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
}
