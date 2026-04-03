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
use Illuminate\Support\Collection;
use RalphJSmit\Laravel\SEO\Schema\ArticleSchema;
use RalphJSmit\Laravel\SEO\Schema\BreadcrumbListSchema;
use RalphJSmit\Laravel\SEO\Schema\FaqPageSchema;
use RalphJSmit\Laravel\SEO\SchemaCollection;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Article extends Model implements HasMedia
{
    use HasFactory,
        HasImage,
        HasSEO,
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
    const TAG_REVIEWS = 'Reviews';

    const INTERNAL_TAG = [
        self::TAG_TOP_INSIGHTS,
        self::TAG_REVIEWS
    ];

    protected $dates = ['date'];

    protected $casts = [
        'faq' => 'array',
    ];

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

    public function scopeReviews($query)
    {
        return $query->whereHas('tags', function(Builder $query) {
            $query->where('name', self::TAG_REVIEWS);
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

    public function getSmallFeaturedImageUrlAttribute(): ?string
    {
        if ($this->getFirstMedia('featured')) {
            return $this->getFirstMedia('featured')->getUrl('small');
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

    public function getAuthorBylineAttribute(): string
    {
        $main = $this->relationLoaded('authors')
            ? $this->authors->first()
            : $this->authors()->first();

        if ($main) {
            return $main->byline ?? $main->name;
        }

        return 'Spannr';
    }

    public function getDynamicSEOData(): SEOData
    {
        $this->loadMissing('seo');

        $seoTitle = $this->seo?->title;
        $documentTitle = $seoTitle
            ?: ($this->page_title ?: ($this->name . ' - Spannr'));
        $openGraphTitle = $seoTitle ?: $this->name;

        $description = $this->seo?->description ?: $this->meta_description;
        if (! $description && $this->excerpt) {
            $description = Str::limit(strip_tags($this->excerpt), 160);
        }

        $image = $this->seo?->image ?: $this->shareable_image_url;

        $schema = SchemaCollection::initialize()
            ->addBreadcrumbs(function (BreadcrumbListSchema $breadcrumbs, SEOData $SEOData): BreadcrumbListSchema {
                return $breadcrumbs
                    ->prependBreadcrumbs([
                        'Spannr' => route('home'),
                        'Insights' => route('public.content.articles.index'),
                    ])
                    ->markup(function (Collection $markup): Collection {
                        $items = collect($markup->get('itemListElement'));
                        $last = $items->pop();
                        if (is_array($last)) {
                            $last['name'] = $this->name;
                        }

                        return $markup->put('itemListElement', $items->push($last)->values());
                    });
            })
            ->addArticle(function (ArticleSchema $article, SEOData $SEOData): ArticleSchema {
                return $article->markup(function (Collection $markup): Collection {
                    $markup = $markup->put('publisher', [
                        '@type' => 'Organization',
                        'name' => 'Spannr',
                        'url' => route('home'),
                    ])->put('headline', $this->name);

                    $main = $this->relationLoaded('authors')
                        ? $this->authors->first()
                        : $this->authors()->first();

                    if ($main) {
                        $markup = $markup->put('author', [
                            '@type' => 'Person',
                            'name' => $this->author_byline,
                            'url' => $main->about_page_url,
                            'jobTitle' => $main->title,
                        ]);
                    }

                    return $markup;
                });
            });

        if ($this->status === self::STATUS_PUBLISHED && $this->faq) {
            $faqPairs = collect($this->faq)->filter(function ($row) {
                return ! empty(trim((string) ($row['question'] ?? '')))
                    && ! empty(trim((string) ($row['answer'] ?? '')));
            });
            if ($faqPairs->isNotEmpty()) {
                $schema->addFaqPage(function (FaqPageSchema $faqPage, SEOData $SEOData) use ($faqPairs): FaqPageSchema {
                    foreach ($faqPairs as $pair) {
                        $faqPage->addQuestion(
                            (string) $pair['question'],
                            (string) $pair['answer']
                        );
                    }

                    return $faqPage;
                });
            }
        }

        $tagNames = $this->relationLoaded('tags')
            ? $this->tags->pluck('name')->filter()->values()->all()
            : null;

        $robots = $this->status === self::STATUS_PUBLISHED
            ? 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1'
            : 'noindex, nofollow';

        return new SEOData(
            title: $documentTitle,
            openGraphTitle: $openGraphTitle,
            description: $description,
            author: $this->author_byline,
            image: $image,
            url: $this->url,
            enableTitleSuffix: false,
            published_time: Carbon::parse($this->date),
            modified_time: $this->updated_at,
            type: 'article',
            site_name: 'Spannr',
            locale: 'en_US',
            section: 'Insights',
            tags: $tagNames,
            schema: $schema,
            robots: $robots,
        );
    }

    /**
     * @param  array<int, array{question?: string, answer?: string}|mixed>|null  $faq
     * @return array<int, array{question: string, answer: string}>|null
     */
    public static function normalizeFaqPayload(?array $faq): ?array
    {
        if (! $faq) {
            return null;
        }

        $out = collect($faq)
            ->map(function ($row) {
                if (! is_array($row)) {
                    return null;
                }

                return [
                    'question' => trim((string) ($row['question'] ?? '')),
                    'answer' => trim((string) ($row['answer'] ?? '')),
                ];
            })
            ->filter(fn ($row) => $row && $row['question'] !== '' && $row['answer'] !== '')
            ->values()
            ->all();

        return $out === [] ? null : $out;
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
