<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Location extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Relationships
     */
    public function events(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Event::class, 'locatable', 'location_relationships')->withTimestamps();
    }

    public function investors(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Investor::class, 'locatable', 'location_relationships')->withTimestamps();
    }

    public function jobs(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Job::class, 'locatable', 'location_relationships')->withTimestamps();
    }

    public function organizations(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Organization::class, 'locatable', 'location_relationships')->withTimestamps();
    }

    public function people(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Person::class, 'locatable', 'location_relationships')->withTimestamps();
    }

    /**
     * Mutators
     */
    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::of($value)->slug('-');
    }
}
