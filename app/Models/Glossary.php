<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Glossary extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'glossary';
    protected $guarded = [];

    private string $modelName = 'glossary';

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
        return route('public.content.articles.show', $this->slug);
    }
}
