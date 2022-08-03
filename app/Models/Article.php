<?php

namespace App\Models;

use App\Http\Traits\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory,
        HasImage;

    public $incrementing = false;
    protected $guarded = [];

    private string $searchableRouteName = 'public.content.articles'; // TODO: update all these!!
    private string $searchableRouteField = 'slug';
    private string $searchableIcon = 'fa-duotone fa-buildings';

    private string $modelName = 'articles';
    private string $imageUrlStoragePath = 'public/images/articles';

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
        return route('public.content.articles.show', $this->slug);
    }
}
