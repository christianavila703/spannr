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
    const ROLE_RECRUITER = 'Recruiter';
    const ROLE_REVIEWER = 'Reviewer';
    const ROLE_HIRING_MANAGER = 'Hiring Manager';
    const ROLE_MARKETPLACE_MANAGER = 'Marketplace Manager';

    const COLORS = [
        self::ROLE_SUPER_ADMIN => 'bg-dark',
        self::ROLE_ADMIN => 'bg-tertiary',
        self::ROLE_AUTHOR => 'bg-success',
        self::ROLE_SPANNR_TEAM => 'bg-quaternary',
        self::ROLE_SPANNR_CONTRIBUTOR => 'bg-primary',
        self::ROLE_RECRUITER => 'bg-info text-dark',
        self::ROLE_REVIEWER => 'bg-warning text-dark'
    ];

    public function getColorAttribute(): string
    {
        return match($this->name) {
            self::ROLE_SUPER_ADMIN => 'bg-dark',
            self::ROLE_ADMIN => 'bg-tertiary',
            self::ROLE_AUTHOR => 'bg-success',
            self::ROLE_SPANNR_TEAM => 'bg-quaternary',
            self::ROLE_SPANNR_CONTRIBUTOR => 'bg-primary',
            self::ROLE_RECRUITER => 'bg-info text-dark',
            self::ROLE_REVIEWER => 'bg-warning text-dark',
            default => 'bg-secondary'
        };
    }
}
