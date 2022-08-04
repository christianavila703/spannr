<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaPublisher extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function mediaItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MediaItem::class);
    }
}
