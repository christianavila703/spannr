<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class JobApplication extends Model implements HasMedia
{
    use HasFactory,
        InteractsWithMedia;

    protected $guarded = ['id'];

    const STATUS_NEW = 'New';
    const STATUS_PENDING = 'Pending';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_ARCHIVED = 'Archived';

    const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_PENDING,
        self::STATUS_IN_PROGRESS,
        self::STATUS_ARCHIVED
    ];

    /* Relationships */
    public function person(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function job(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function location(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /* Attributes */
    public function getPrettyCreatedAtAttribute(): string
    {
        return Carbon::parse($this->created_at)->format('M j, Y');
    }

    public function getNameAttribute(): string
    {
        return $this->person->name . " " . $this->job->name;
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            self::STATUS_NEW => 'bg-warning-neon text-dark',
            self::STATUS_IN_PROGRESS => 'bg-info text-dark',
            self::STATUS_PENDING => 'bg-quaternary',
            self::STATUS_ARCHIVED => 'bg-light text-muted',
            default => 'bg-danger'
        };
    }

    public function getAdminLinkUrlAttribute(): string
    {
        return route('admin.recruiting.applications', 'app=' . $this->id);
    }

    /* Functions */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('resume')
            ->singleFile();
    }
}
