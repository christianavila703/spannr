<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketplaceLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const TYPE_WEBSITE_VISIT = 'Website Visit';
    const TYPE_LISTING_CLICK_FROM_INDEX = 'Listing Click';
    const TYPE_LISTING_EMAIL = 'Listing Email';
    const TYPE_ZIP_CODE_SEARCH = 'Zip Code Search';
    const TYPE_MARKETPLACE_SEARCH = 'Marketplace Search';

    protected $casts = [
        'data' => 'array'
    ];

    /**
     * Relationships
     */
    public function entity(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function listing(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
