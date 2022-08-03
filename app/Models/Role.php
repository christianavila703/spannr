<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends \Spatie\Permission\Models\Role
{

    const ROLE_SUPER_ADMIN = 'Super Admin';
    const ROLE_ADMIN = 'Admin';
    const ROLE_USER_FREE = 'Free User';

    public function getColorAttribute(): string
    {
        return match($this->name) {
            self::ROLE_SUPER_ADMIN => 'bg-quaternary',
            self::ROLE_ADMIN => 'bg-tertiary',
            default => 'bg-info'
        };
    }
}
