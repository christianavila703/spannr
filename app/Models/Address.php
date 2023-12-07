<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Address extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::created(function ($address) {
            $secondLine = $address->city . ', ' . $address->state . ', ' . $address->country;
            $location = Location::where('name', $secondLine)->first();
            if ($location) {
                $address->location_id = $location->id;
                $address->save();
            } else {
                // TODO: Create a location for city/state/zip with 'central' lat/lng
            }
        });
    }

    /**
     * Relationships
     */
    public function addressable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Mutators
     */
    public function setCountryAttribute($value) {
        if ($value === 'United States') {
            $this->attributes['country'] = 'USA';
        }
    }

    public function setNameAttribute($value) {
        $name = str_replace('United States of America', 'USA', $value);
        $this->attributes['name'] = $name;
    }

    /**
     * Accessors
     */
    public function getGoogleMapsUrlAttribute(): string
    {
        return "https://www.google.com/maps/search/{$this->addressable->name}+{$this->name}/@{$this->latitude},{$this->longitude},15z";
    }

    /**
     * Scopes
     */
    public function scopeDistance($query, $from_latitude, $from_longitude, $distance)
    {
        $raw = DB::raw('ROUND ( ( 3959 * acos( cos( radians(' . $from_latitude . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $from_longitude . ') ) + sin( radians(' . $from_latitude . ') ) * sin( radians( latitude ) ) ) ) ) AS distance');
        return $query->select('*')->addSelect($raw)->orderBy('distance', 'ASC')->having('distance', '<=', $distance);
    }

    public function getDistanceFrom($lat1, $lon1, $lat2, $lon2): int|string
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            return number_format($dist * 60 * 1.1515, 2);
        }
    }
}
