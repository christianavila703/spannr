<?php

namespace App\Models;

use App\Http\Traits\HasImage;
use App\Http\Traits\SearchableEntity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Event extends Model
{
    use HasFactory,
        LogsActivity,
        SearchableEntity,
        HasImage;

    protected $guarded = ['id'];

    const STATUS_CURRENT = 'current';
    const STATUS_PAST = 'past';

    private string $searchableRouteName = 'events.show';
    private string $searchableRouteField = 'slug';
    private string $searchableIcon = 'fa-duotone fa-calendar';

    private string $modelName = 'events';
    private string $imageUrlStoragePath = 'public/images/events';

    private string $blankImage = '/images/blank-event.png';

    /**
     * Functions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(Activity::LOG_EVENTS)
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => $this->name . " was {$eventName}");
    }

    /**
     * Relationships
     */
    public function eventTypes(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(EventType::class, 'relatable' , 'event_relationships')->withTimestamps();
    }

    public function investors(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Investor::class, 'relatable' , 'event_relationships')->withTimestamps();
    }

    public function locations(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Location::class, 'locatable' , 'location_relationships')->withTimestamps();
    }

    public function organizations(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Organization::class, 'relatable' , 'event_relationships')->withTimestamps();
    }

    public function people(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Person::class, 'relatable' , 'event_relationships')->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopePast(Builder $query): Builder
    {
        return $query->where('start_date', '<', Carbon::now());
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('start_date', '>=', Carbon::now());
    }

    /**
     * Accessors
     */
    public function getWebsiteLinkAttribute(): string
    {
        return '<a href="' . $this->website . '" target="_blank" rel="noopener noreferrer">' . $this->website . '</a>';
    }

    public function getSummaryAttribute(): string
    {
        if (str_word_count($this->description) > 20) {
            return implode(' ', array_slice(explode(' ', $this->description), 0, 20)) . '...';
        } else {
         return $this->description;
        }
    }

    public function getStatusAttribute(): string
    {
        $startDate = Carbon::parse($this->start_date)->format('Y-m-d');

        if ($startDate < Carbon::now()->format('Y-m-d')) {
            return self::STATUS_PAST;
        } else {
            return self::STATUS_CURRENT;
        }
    }

    /**
     * Mutators
     */
    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::of($value)->slug('-');
    }
}
