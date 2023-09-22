<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function events(): \Illuminate\Database\Eloquent\Relations\morphToMany
    {
        return $this->morphToMany(Event::class, 'relatable', 'event_relationships');
    }
}
