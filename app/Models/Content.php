<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Content extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = [];
    protected $table = 'content';

    const STATUS_PUBLISHED = 'Published';
    const STATUS_DRAFT = 'Draft';

    const STATUSES = [
        self::STATUS_PUBLISHED,
        self::STATUS_DRAFT
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
        'reports--2022-longevity-funding',
        'guide-to-longevity',
        'investors'
    ];

    /**
     * Scopes
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Relationships
     */
    public function tags(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Content::class, 'parent_id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Content::class, 'parent_id')->orderBy('order')->orderBy('name');
    }

    /**
     * Accessors
     */
    public function getUrlAttribute(): string
    {
        return $this->link;
    }

    public function getShareableFacebookUrlAttribute(): string
    {
        return 'https://www.facebook.com/sharer.php?u=' . urlencode('https://spannr.com/reports/' . $this->slug);
    }

    public function getShareableTwitterUrlAttribute(): string
    {
        return 'https://twitter.com/intent/tweet?text=' . urlencode($this->name . ' https://spannr.com/reports/' . $this->slug);
    }

    public function getShareableLinkedinUrlAttribute(): string
    {
        return 'https://www.linkedin.com/shareArticle?mini=true&url=' . urlencode('https://spannr.com/reports/' . $this->slug) . '&title=' . urlencode($this->name);
    }

    public function getShareableRedditUrlAttribute(): string
    {
        return 'https://www.reddit.com/submit?url=' . urlencode('https://spannr.com/reports/' . $this->slug) . '&title=' . urlencode($this->name);
    }

    public function getShareableHackerNewsUrlAttribute(): string
    {
        return 'https://news.ycombinator.com/submitlink?u=' . urlencode('https://spannr.com/reports/' . $this->slug) . '&t=' . $this->name;
    }

    public function getReadingTimeMinutesAttribute(): string
    {
        $word_count = str_word_count( strip_tags($this->content) );
        return ceil($word_count / 100);
    }

    public function getShareableImageUrlAttribute(): string
    {
        if ($this->getFirstMediaUrl('featured')) {
            return $this->getFirstMediaUrl('featured');
        }

        return 'https://wp.spannr.com/wp-content/uploads/2022/04/social-share-white.jpg';
    }

    public function getIsChildAttribute(): bool
    {
        return $this->parent_id !== null;
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

    public function setSlugAttribute($value) {
        $this->attributes['slug'] = $value;

        if ($this->parent_id) {
            $this->attributes['link'] = $this->parent->link . "#" . $value;
        } else {
            $link = url('/');

            $slugParts = explode('--', $value);

            foreach ($slugParts as $part) {
                $link .= "/" . $part;
            }

            $this->attributes['link'] = $link;
        }
    }

    private function replaceUrls(string $value): string
    {
        $newValue = str_replace('//wp.spannr.com', '//spannr.com', $value);
        return str_replace('//spannr.com/wp-content/uploads', '//wp.spannr.com/wp-content/uploads', $newValue);
    }

    /**
     * Functions
     */
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
