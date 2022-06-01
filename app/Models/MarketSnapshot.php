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

    /**
     * FUNCTIONS
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('market-snapshots')
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
     * Accessors
     */
    public function getFormattedIndexChangeAttribute(): string
    {
        return ($this->index_change * 100) . '%';
    }

    public function getFormattedIndexValueAttribute(): string
    {
        return '$' . number_format($this->index_value, 2);
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
