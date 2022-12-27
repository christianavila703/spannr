<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Stock extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE
    ];

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

    public function latestSnapshot(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StockSnapshot::class)->whereHas('marketSnapshot', function (Builder $query) {
                $query->hourly();
            })->latest();

    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeIndexed($query)
    {
        $query->where('indexed', 1);
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
