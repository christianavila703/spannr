<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Stock extends Model
{
    use HasFactory,
        LogsActivity;

    protected $guarded = ['id'];

    /**
     * Relationships
     */
    public function organization(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function snapshots(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StockSnapshot::class)->orderByDesc('created_at');
    }

    /**
     * Functions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('stocks')
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => $this->name . " was {$eventName}");
    }

    /**
     * Mutators
     */
    public function setTickerAttribute($value) {
        $this->attributes['ticker'] = $value;
        $this->attributes['slug'] = Str::of($value)->slug('-');
    }
}
