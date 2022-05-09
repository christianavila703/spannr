<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FundraisingRound extends Model
{
    use HasFactory,
        LogsActivity;

    protected $guarded = ['id'];

    const ROUND_SEED = 'Seed';
    const ROUND_SERIES_A = 'Series A';
    const ROUND_SERIES_B = 'Series B';
    const ROUND_SERIES_C = 'Series C';
    const ROUND_SERIES_D = 'Series D';
    const ROUND_UNKNOWN = 'Unknown';

    const ROUNDS = [
        self::ROUND_SEED,
        self::ROUND_SERIES_A,
        self::ROUND_SERIES_B,
        self::ROUND_SERIES_C,
        self::ROUND_SERIES_D,
        self::ROUND_UNKNOWN
    ];

    /**
     * Relationships
     */
    public function organization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function investors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Investor::class)->withTimestamps();
    }

    /**
     * Functions
     */

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('fundraising-rounds')
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => $this->name . " was {$eventName}");
    }
}
