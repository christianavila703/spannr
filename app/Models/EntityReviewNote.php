<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityReviewNote extends Model
{
    const PRIORITY_LOW = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH = 3;

    const PRIORITIES = [
        self::PRIORITY_LOW => 'Low',
        self::PRIORITY_MEDIUM => 'Medium',
        self::PRIORITY_HIGH => 'High',
    ];

    public function getPriorityColorClassAttribute()
    {
        return match($this->priority) {
            self::PRIORITY_LOW => 'text-success',
            self::PRIORITY_MEDIUM => 'text-warning',
            self::PRIORITY_HIGH => 'text-danger',
            default => ''
        };
    }

    protected $guarded = ['id'];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
