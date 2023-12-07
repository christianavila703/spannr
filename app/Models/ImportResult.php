<?php

namespace App\Models;

use App\Observers\ImportResultObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ImportResult extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $casts = [
        'data' => 'array',
        'exception_errors' => 'array',
        'tag_errors' => 'array',
        'address_errors' => 'array',
        'email_errors' => 'array'
    ];

    const TYPE_LISTING_IMPORT = 'Listing Import';
    const TYPE_LISTING_BATCH = 'Listing Batch';

    const STATUS_PENDING = 'Pending';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_HAS_ERRORS = 'Has Errors';
    const STATUS_FAILED = 'Failed';

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_COMPLETED,
        self::STATUS_FAILED,
        self::STATUS_HAS_ERRORS
    ];

    const ERROR_FIELDS = [
        'exception_errors',
        'tag_errors',
        'address_errors',
        'email_errors'
    ];

    protected static function booted()
    {
        ImportResult::observe(ImportResultObserver::class);
    }

    /* Scopes */
    public function scopeListings($query)
    {
        return $query->whereIn('type', [self::TYPE_LISTING_IMPORT, self::TYPE_LISTING_BATCH]);
    }

    public function scopeListingImports($query)
    {
        return $query->where('type', self::TYPE_LISTING_IMPORT);
    }

    /* Relationships */
    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ImportResult::class, 'parent_id');
    }

    public function entity(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ImportResult::class, 'parent_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* Attributes */
    public function getEntityUrlAttribute(): ?string
    {
        if ($this->entity) {
            if (get_class($this->entity) === Listing::class) {
                return $this->entity->show_url;
            }

            return NULL;
        }

        return null;
    }

    public function getNameAttribute(): string
    {
        $name = Carbon::parse($this->created_at)->format('Y-m-d');

        if ($this->entity) {
            $name .= ' ' . $this->entity->name;
        }

        $name .= ' ' . $this->status;

        return $name;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_COMPLETED => 'text-success',
            self::STATUS_HAS_ERRORS => 'text-quaternary',
            default => 'text-danger'
        };
    }
}
