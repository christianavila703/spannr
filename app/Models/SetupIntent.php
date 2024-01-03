<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetupIntent extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $guarded = [];

    protected $casts = [
        'data' => 'array'
    ];

    const STATUS_CANCELED = 'canceled';
    const STATUS_PROCESSING = 'processing';
    const STATUS_REQUIRES_ACTION = 'requires_action';
    const STATUS_REQUIRES_CONFIRMATION = 'requires_confirmation';
    const STATUS_REQUIRES_PAYMENT_METHOD = 'requires_payment_method';
    const STATUS_SUCCEEDED = 'succeeded';

    const STATUSES_PROCEED_WITH_SETUP_INTENT = [
        self::STATUS_PROCESSING,
        self::STATUS_REQUIRES_ACTION,
        self::STATUS_REQUIRES_CONFIRMATION,
        self::STATUS_REQUIRES_PAYMENT_METHOD,
    ];

    const TYPE_JOB_SUBSCRIPTION = 'Job Subscription';
    const TYPE_SPANNR_PLUS_SUBSCRIPTION = 'Spannr Subscription';

    /* Relationships */
    public function job(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function jobSubscription(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* Accessors */
    public function getStripeUrlAttribute(): string
    {
        return config('service.stripe.url') . '/setup_intents/' . $this->id;
    }
}
