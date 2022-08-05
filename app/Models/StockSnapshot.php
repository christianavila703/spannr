<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;

class StockSnapshot extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relationships
     */
    public function marketSnapshot(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MarketSnapshot::class);
    }

    public function stock(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(Activity::LOG_STOCK_SNAPSHOTS)
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => $this->name . " was {$eventName}");
    }

    /**
     * Accessors
     */
    public function getChangeColorAttribute(): string
    {
        if ($this->previous_day_change_price < 0) {
            return 'text-danger';
        } elseif ($this->previous_day_change_price > 0) {
            return 'text-success';
        } else {
            return 'text-normal';
        }
    }

    public function getFormattedMarketCapAttribute(): string
    {
        if ($this->market_cap != '') {
            return '$' . number_format($this->market_cap, 0);
        } else {
            return '';
        }
    }

    public function getWeightPercentAttribute(): string
    {
        return number_format($this->weight, 2);
    }
}
