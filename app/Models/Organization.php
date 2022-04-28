<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    const TYPE_PRIVATE = 'Private';
    const TYPE_PUBLIC = 'Public';
    const TYPE_NONPROFIT = 'Non-Profit';
    const TYPE_INVESTOR = 'Investor';
    const TYPE_GOVERNMENT = 'Government';
    const TYPE_EDUCATIONAL = 'Educational';
    const TYPE_MEDICAL = 'Medical';
    const TYPE_DAO = 'DAO';

    protected $guarded = ['id'];

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
}
