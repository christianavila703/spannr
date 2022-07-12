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

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

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
            ->useLogName(Activity::LOG_STOCKS)
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => $this->name . " was {$eventName}");
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Accessors
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'text-success',
            self::STATUS_INACTIVE => 'text-danger',
        };
    }

    /**
     * Mutators
     */
    public function setTickerAttribute($value) {
        $this->attributes['ticker'] = $value;
        $this->attributes['slug'] = Str::of($value)->slug('-');
    }
}
