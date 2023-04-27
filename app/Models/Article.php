<?php

namespace App\Models;

use App\Http\Traits\HasImage;
use App\Models\Traits\Lockable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Article extends Model implements HasMedia
{
    use HasFactory,
        HasImage,
        InteractsWithMedia,
        Lockable;

    protected $guarded = [];

    const STATUS_PUBLISHED = 'publish';
    const STATUS_DRAFT = 'draft';

    const STATUSES = [
        self::STATUS_PUBLISHED,
        self::STATUS_DRAFT
    ];

    // 'Protected' Tags -- used in layouts
    const TAG_INVESTING = 'Investing';
    const TAG_TOP_INSIGHTS = 'Top Insights';

    protected $dates = ['date'];

    private string $searchableRouteName = 'public.content.articles'; // TODO: update all these!!
    private string $searchableRouteField = 'slug';
    private string $searchableIcon = 'fa-duotone fa-buildings';

    private string $modelName = 'articles';
    private string $imageUrlStoragePath = 'public/images/articles';

    /**
     * Relationships
     */
    public function authors(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function tags(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeInvesting($query)
    {
        return $query->whereHas('tags', function(Builder $query) {
            $query->where('name', self::TAG_INVESTING);
        });
    }

    public function scopeTopInsights($query)
    {
        return $query->whereHas('tags', function(Builder $query) {
            $query->where('name', self::TAG_TOP_INSIGHTS);
        });
    }

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
        return route('public.content.articles.show', $this->slug);
    }

    public function getFormattedDateAttribute(): string
    {
        return Carbon::parse($this->date)->format('M d, Y H:i');
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

    public function getMetaDescriptionCharacterCountAttribute(): int
    {
        return strlen($this->meta_description);
    }

    public function getMetaDescriptionCharacterCountColorAttribute(): string
    {
        $count = strlen($this->meta_description);

        return match(true) {
            $count >= 50 && $count <= 160 => 'success',
            $count > 160 => 'danger',
            default => 'dark'
        };
    }

    public function getFeaturedImageUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('featured') ?? NULL;
    }

    public function getMediumFeaturedImageUrlAttribute(): ?string
    {
        if ($this->getFirstMedia('featured')) {
            return $this->getFirstMedia('featured')->getUrl('medium');
        } else {
            return NULL;
        }
    }

    public function getShareableImageUrlAttribute(): string
    {
        if ($this->getFirstMediaUrl('featured')) {
            return $this->getFirstMediaUrl('featured');
        }

        if ($this->image) {
            return url('/') . '/storage/images/' . $this->modelName . '/' . $this->image;
        }

        return 'https://wp.spannr.com/wp-content/uploads/2022/04/social-share-white.jpg';
    }

    public function getShortExcerptAttribute(): string
    {
         return Str::words($this->excerpt, 20);
    }

    public function getMetaPublishedTimeAttribute(): string
    {
        return Carbon::parse($this->date)->toAtomString();
    }

    public function getReadingTimeMinutesAttribute(): string
    {
        $word_count = str_word_count( strip_tags($this->content) );
        return ceil($word_count / 100);
    }

    public function getMainAuthorAttribute(): User|null
    {
        return $this->authors()->first();
    }

    /**
     * Functions
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->keepOriginalImageFormat()
            ->crop(Manipulations::CROP_CENTER, 300, 300)
            ->nonQueued();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('small')
                    ->width(488)
                    ->height(275)
                    ->nonQueued();

                $this
                    ->addMediaConversion('medium')
                    ->width(768)
                    ->height(432)
                    ->nonQueued();
        });

        $this->addMediaCollection('images')
            ->registerMediaConversions(function (Media $media) {
                $this
                    ->addMediaConversion('post')
                    ->width(600)
                    ->keepOriginalImageFormat()
                    ->nonQueued();
        });
    }
}
