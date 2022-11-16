<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MediaItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relationships
     */
    public function publisher(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MediaPublisher::class);
    }

    public function organizations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Organization::class)->withTimestamps();
    }

    /**
     * Accessors
     */
    public function getFormattedDateAttribute(): string
    {
        return Carbon::parse($this->date)->format('M d, Y');
    }

    public function getShortSummaryAttribute(): string
    {
        if ($this->summary) {
            return Str::words($this->summary, 25);
        } else {
            return '';
        }
    }
}
