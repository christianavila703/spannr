<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $guarded = [];
    protected $table = 'content';

    const STATUS_PUBLISHED = 'Published';
    const STATUS_PRIVATE = 'Private';
    const STATUS_DRAFT = 'Draft';

    const STATUSES = [
        self::STATUS_PUBLISHED,
        self::STATUS_PRIVATE,
        self::STATUS_DRAFT,
    ];

    const PAGES = [
        'home',
        'articles',
        'index',
        'index-about',
        'index-terms',
        'ventures',
        'terms',
        'privacy',
        'contact',
        'confirmed',
        'articles',
        'glossary',
        'companies',
    ];
}
