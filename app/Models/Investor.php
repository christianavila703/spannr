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

class Investor extends Model
{
    use HasFactory,
        HasSocialMedia,
        LogsActivity,
        SearchableEntity,
        HasImage;

    protected $guarded = ['id'];

    const TYPE_ANGEL = 'Angel Investor';
    const TYPE_FUND = 'Fund';
    const TYPE_PRIVATE_EQUITY = 'Private Equity';

    const TYPES = [
        self::TYPE_ANGEL,
        self::TYPE_FUND,
        self::TYPE_PRIVATE_EQUITY,
    ];

    private string $searchableRouteName = 'investors.show';
    private string $searchableRouteField = 'slug';
    private string $searchableIcon = 'fa-duotone fa-hands-holding-dollar';

    private string $modelName = 'investors';
    private string $imageUrlStoragePath = 'public/images/investors';

    /**
     * Relationships
     */
    public function fundraisingRounds(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(FundraisingRound::class);
    }

    public function locations(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Location::class, 'locatable' , 'location_relationships')->withTimestamps();
    }

    public function organizations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Organization::class);
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
}
