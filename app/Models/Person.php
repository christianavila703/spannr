<?php

namespace App\Models;

use App\Http\Traits\HasSocialMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Person extends Model
{
    use HasFactory,
        HasSocialMedia;

    const ORGANIZATION_ROLE_FOUNDER = 'Founder';
    const ORGANIZATION_ROLE_LEADERSHIP = 'Leadership';
    const ORGANIZATION_ROLE_OTHER = 'Other';

    const ORGANIZATION_ROLES = [
        self::ORGANIZATION_ROLE_FOUNDER,
        self::ORGANIZATION_ROLE_LEADERSHIP,
        self::ORGANIZATION_ROLE_OTHER
    ];

    protected $guarded = ['id'];

    /**
     * Mutators
     */
    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::of($value)->slug('-');
    }

    public function organizations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Organization::class)->withPivot('role')->withTimestamps();
    }
}
