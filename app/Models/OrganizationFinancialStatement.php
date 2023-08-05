<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationFinancialStatement extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
        'organization_id',
        'record_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'tickers' => 'array',
        'financials' => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
