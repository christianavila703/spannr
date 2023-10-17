<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    const SLACK_NOTIFICATION_CHANNEL = 'website';

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_CANCELLED = 2;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
