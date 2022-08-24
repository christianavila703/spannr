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
        'index--about',
        'index--terms-and-disclaimers',
        'ventures',
        'terms',
        'privacy',
        'contact',
        'confirmed',
        'articles',
        'glossary',
        'companies',
        'events--leveraging-longevity-sd',
    ];

    /**
     * Relationships
     */
    public function tags(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Mutators
     */
    public function setContentAttribute($value) {
        $this->attributes['content'] = $this->replaceUrls($value);
    }

    public function setSeoAttribute($value) {
        $this->attributes['seo'] = $this->replaceUrls($value);
    }

    private function replaceUrls(string $value): string
    {
        $newValue = str_replace('//wp.spannr.com', '//spannr.com', $value);
        return str_replace('//spannr.com/wp-content/uploads', '//wp.spannr.com/wp-content/uploads', $newValue);
    }
}
