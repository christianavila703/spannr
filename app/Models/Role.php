<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends \Spatie\Permission\Models\Role
{

    const ROLE_SUPER_ADMIN = 'Super Admin';
    const ROLE_ADMIN = 'Admin';
    const ROLE_AUTHOR = 'Author';
    const ROLE_USER_FREE = 'Free User';
    const ROLE_SPANNR_TEAM = 'Spannr Team';
    const ROLE_SPANNR_CONTRIBUTOR = 'Spannr Contributor';
    const ROLE_REVIEWER = 'Reviewer';

    public function getColorAttribute(): string
    {
        return match($this->name) {
            self::ROLE_SUPER_ADMIN => 'bg-quaternary',
            self::ROLE_ADMIN => 'bg-tertiary',
            self::ROLE_AUTHOR => 'bg-success',
            self::ROLE_SPANNR_TEAM => 'bg-dark',
            self::ROLE_SPANNR_CONTRIBUTOR => 'bg-danger',
            default => 'bg-secondary'
        };
    }
}
