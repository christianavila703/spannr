<?php

namespace App\Models;

use App\Http\Traits\SearchableEntity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MarketSnapshot extends Model
{
    use HasFactory,
        LogsActivity,
        SearchableEntity;

    protected $guarded = ['id'];

    const TYPE_DAILY = 'daily';
    const TYPE_HOURLY = 'hourly';

    /**
     * FUNCTIONS
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(Activity::LOG_MARKET_SNAPSHOTS)
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => $this->name . " was {$eventName}");
    }

    /**
     * Relationships
     */
    public function stockSnapshots(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StockSnapshot::class)->orderByDesc('market_cap');
    }

    /**
     * SCOPES
     */
    public function scopeDaily($query)
    {
        return $query->where('type', self::TYPE_DAILY);
    }

    public function scopeHourly($query)
    {
        return $query->where('type', self::TYPE_HOURLY);
    }

    /**
     * Accessors
     */
    public function getFormattedDateAttribute(): string
    {
        return Carbon::parse($this->timestamp)->format('Y-m-d');
    }

    public function getFormattedPrettyDateAttribute(): string
    {
        return Carbon::parse($this->timestamp)->format('M j, Y');
    }

    public function getFormattedDateJsonAttribute(): string
    {
        return Carbon::parse($this->timestamp)->timestamp;
    }

    public function getFormattedIndexChangeAttribute(): string
    {
        if ($this->index_change != '') {
            return number_format(($this->index_change * 100), 2) . '%';
        } else {
            return '';
        }
    }

    public function getFormattedIndexValueAttribute(): string
    {
        if ($this->index_value != '') {
            return '$' . number_format($this->index_value, 2);
        } else {
            return '';
        }
    }

    public function getFormattedTimestampAttribute(): string
    {
        return Carbon::parse($this->timestamp)->format('M d, Y g:i a');
    }

    public function getFormattedTimezoneAttribute(): string
    {
        return Carbon::parse($this->timestamp)->format('e');
    }

    public function getIndexChangeColorAttribute(): string
    {
        if ($this->index_value >= 100) {
            return 'text-success';
        } else {
            return 'text-danger';
        }
    }
}
