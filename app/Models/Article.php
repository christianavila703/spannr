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

    public function getShareableFacebookUrlAttribute(): string
    {
        return 'https://www.facebook.com/sharer.php?u=' . urlencode('https://spannr.com/articles/' . $this->slug);
    }

    public function getShareableTwitterUrlAttribute(): string
    {
        return 'https://twitter.com/intent/tweet?text=' . urlencode($this->name . ' https://spannr.com/articles/' . $this->slug);
    }

    public function getShareableLinkedinUrlAttribute(): string
    {
        return 'https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode('https://spannr.com/articles/' . $this->slug) . '&title=' . urlencode($this->name);
    }

    public function getShareableRedditUrlAttribute(): string
    {
        return 'https://www.reddit.com/submit?url=' . urlencode('https://spannr.com/articles/' . $this->slug) . '&title=' . urlencode($this->name);
    }

    public function getShareableHackerNewsUrlAttribute(): string
    {
        return 'https://news.ycombinator.com/submitlink?u=' . urlencode('https://spannr.com/articles/' . $this->slug) . '&t=' . $this->name;
    }
}
