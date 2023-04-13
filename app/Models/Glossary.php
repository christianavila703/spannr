<?php

namespace App\Models;

use App\Models\Traits\Lockable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Glossary extends Model
{
    use HasFactory,
        Lockable;

    public $incrementing = false;
    protected $table = 'glossary';
    protected $guarded = [];

    const STATUS_PUBLISHED = 'publish';
    const STATUS_DRAFT = 'draft';

    const STATUSES = [
        self::STATUS_PUBLISHED,
        self::STATUS_DRAFT
    ];

    private string $modelName = 'glossary';

    /**
     * Scopes
     */

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopePublished(Builder $query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Attributes
     */
    public function getHtmlEncodedNameAttribute(): string
    {
        return htmlentities($this->name);
    }

    public function getHtmlEncodedExcerptAttribute(): string
    {
        return htmlentities(Str::words($this->excerpt, 25));
    }

    public function getUrlAttribute(): string
    {
        return route('public.content.glossary.show', $this->slug);
    }

    public function getFormattedCreatedDateAttribute(): string
    {
        return Carbon::parse($this->created_at)->format('M d, Y H:i');
    }

    public function getFormattedUpdatedDateAttribute(): string
    {
        return Carbon::parse($this->updated_at)->format('M d, Y H:i');
    }

    public function getContentWordCountAttribute(): string
    {
        return strip_tags(str_word_count($this->content));
    }

    public function getExcerptWordCountAttribute(): string
    {
        return strip_tags(str_word_count($this->excerpt));
    }

    public function getShortExcerptAttribute(): string
    {
        return Str::words($this->excerpt, 20);
    }

    /**
     * Mutators
     */
    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['group'] = substr($value, 0, 1);
    }
}
