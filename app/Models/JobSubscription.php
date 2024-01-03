<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobSubscription extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime'
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELED = 'canceled';

    /* Scopes */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /* Relationships */
    public function job(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /* Attributes */
    public function getCardNameAttribute(): ?string
    {
        if ($this->last4) {
            return ucwords($this->brand) . ' ' . $this->last4 . ' ' . $this->exp_month . '/' . $this->exp_year;
        }

        return NULL;
    }

    public function getPrettyCurrentPeriodEndAttribute(): ?string
    {
        if ($this->current_period_end) {
            return Carbon::parse($this->current_period_end)->format('M d, Y');
        }

        return NULL;
    }
}
