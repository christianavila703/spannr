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
    // Date and Time
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

    public function getFormattedTimestampAttribute(): string
    {
        return Carbon::parse($this->timestamp)->format('M d, Y g:i a');
    }

    public function getFormattedTimezoneAttribute(): string
    {
        return Carbon::parse($this->timestamp)->format('T');
    }

    // Change
    public function getFormattedCumulativeChangeAttribute(): string
    {
        if ($this->cumulative_change != '') {
            return number_format(($this->cumulative_change * 100), 2) . '%';
        } else {
            return '';
        }
    }

    public function getFormattedIndexChangeAttribute(): string
    {
        if ($this->index_change != '') {
            return number_format(($this->index_change * 100), 2) . '%';
        } else {
            return '';
        }
    }

    public function getIndexChangeColorAttribute(): string
    {
        if ($this->index_value >= 100) {
            return 'text-success';
        } else {
            return 'text-danger';
        }
    }

    public function getIndexChangeIconAttribute(): string
    {
        if ($this->index_value >= 100) {
            return '<i class="fa-duotone fa-circle-up"></i>';
        } else {
            return '<i class="fa-duotone fa-circle-down"></i>';
        }
    }

    // Value
    public function getFormattedIndexValueAttribute(): string
    {
        if ($this->index_value != '') {
            return '$' . number_format($this->index_value, 2);
        } else {
            return '';
        }
    }

    // Market Cap
    public function getFormattedMarketCapAttribute(): string
    {
        if ($this->market_cap != '') {
            return '$' . number_format($this->market_cap, 0);
        } else {
            return '';
        }
    }

    // Stock Calcs
    public function getVolumeAttribute(): string
    {
        return number_format($this->stockSnapshots()->sum('volume'), 0);
    }

    // Previous Market Snapshot Calcs
    public function getPreviousCloseAttribute(): string
    {
        $previous = $this->where('id', '<', $this->id)->orderByDesc('id')->first();
        return $previous->formatted_index_value ?? '';
    }

    public function getPreviousDollarChangeAttribute(): string
    {
        $previous = $this->where('id', '<', $this->id)->orderByDesc('id')->first();
        return '$' . number_format($this->index_value - $previous->index_value, 2);
    }

    public function getYearHighAttribute(): string
    {
        $highestYear = $this->orderByDesc('index_value')->first();
        return '$' . number_format($highestYear->index_value, 2);
    }

    public function getYearLowAttribute(): string
    {
        $highestYear = $this->orderBy('index_value')->first();
        return '$' . number_format($highestYear->index_value, 2);
    }
}
