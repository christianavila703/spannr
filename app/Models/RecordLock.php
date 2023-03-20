<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecordLock extends Model
{
    const UPDATED_AT = null;

    protected $guarded = ['id'];

    public function lockable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
