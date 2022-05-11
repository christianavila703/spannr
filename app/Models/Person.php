<?php

namespace App\Models;

use App\Http\Traits\HasSocialMedia;
use App\Http\Traits\SearchableEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Person extends Model
{
    use HasFactory,
        HasSocialMedia,
        LogsActivity,
        SearchableEntity;

    const ORGANIZATION_ROLE_FOUNDER = 'Founder';
    const ORGANIZATION_ROLE_TEAM = 'Team';

    const ORGANIZATION_ROLES = [
        self::ORGANIZATION_ROLE_FOUNDER,
        self::ORGANIZATION_ROLE_TEAM
    ];

    protected $guarded = ['id'];

    private string $searchableRouteName = 'people.show';
    private string $searchableRouteField = 'slug';
    private string $searchableIcon = 'fa-duotone fa-user-hair-buns';

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
    public function organizations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Organization::class)->withPivot('role')->withTimestamps();
    }

    /**
     * Functions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('people')
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => $this->name . " was {$eventName}");
    }

}
