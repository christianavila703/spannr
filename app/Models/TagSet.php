<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagSet extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    const TAG_SET_MEDICAL_TREATMENTS = 'Medical Provider Treatments';
    const TAG_SET_CLINIC_FOCUS = 'Clinic Focus';

    /**
     * Relationships
     */
    public function tags(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }
}
