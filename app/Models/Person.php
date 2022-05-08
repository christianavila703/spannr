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
    const ORGANIZATION_ROLE_TEAM = 'Team';

    const ORGANIZATION_ROLES = [
        self::ORGANIZATION_ROLE_FOUNDER,
        self::ORGANIZATION_ROLE_TEAM
    ];

    protected $guarded = ['id'];

    /**
     * Scopes
     */
    public function scopeFounders($query) {
        return $query->where('role', self::ORGANIZATION_ROLE_FOUNDER);
    }

    public function scopeTeam($query) {
        return $query->where('role', self::ORGANIZATION_ROLE_TEAM);
    }

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
