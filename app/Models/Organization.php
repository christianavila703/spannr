<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const TYPE_PRIVATE = 'Private';
    const TYPE_PUBLIC = 'Public';
    const TYPE_NONPROFIT = 'Non-Profit';
    const TYPE_INVESTOR = 'Investor';
    const TYPE_GOVERNMENT = 'Government';
    const TYPE_EDUCATIONAL = 'Educational';
    const TYPE_MEDICAL = 'Medical';
    const TYPE_DAO = 'DAO';

    const TYPES = [
        self::TYPE_PRIVATE,
        self::TYPE_PUBLIC,
        self::TYPE_NONPROFIT,
        self::TYPE_INVESTOR,
        self::TYPE_GOVERNMENT,
        self::TYPE_EDUCATIONAL,
        self::TYPE_MEDICAL,
        self::TYPE_DAO
    ];

    /**
     * Relationships
     */
    public function fundraisingRounds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FundraisingRound::class);
    }

    public function investors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Investor::class);
    }

    /**
     * Mutators
     */
    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::of($value)->slug('-');
    }
}
