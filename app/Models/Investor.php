<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Investor extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const TYPE_ANGEL = 'Angel Investor';
    const TYPE_FUND = 'Fund';
    const TYPE_PRIVATE_EQUITY = 'Private Equity';

    const TYPES = [
        self::TYPE_ANGEL,
        self::TYPE_FUND,
        self::TYPE_PRIVATE_EQUITY,
    ];

    /**
     * Relationships
     */
    public function fundraisingRounds(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(FundraisingRound::class);
    }

    public function organizations(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Organization::class);
    }

    /**
     * Mutators
     */
    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::of($value)->slug('-');
    }
}
