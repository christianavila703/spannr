<?php

namespace App\Models;

use App\Http\Traits\SearchableEntity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Http\Traits\HasBreadcrumbs;

class Tag extends Model
{
    use HasFactory,
        HasBreadcrumbs,
        SearchableEntity;

    protected $guarded = ['id'];

    protected $casts = [
        'entity_types' => 'array'
    ];

    private string $searchableRouteName = 'admin.tags.show';
    private string $searchableRouteField = 'slug';
    private string $searchableIcon = 'fa-duotone fa-tag';

    private string $modelName = 'tags';

    const TAG_PEOPLE = 'People';
    const TAG_JOB_APPLICANT = 'Job Applicant';
    const TAG_HIRING_MANAGER = 'Hiring Manager';
    const TAG_PARENT_ARTICLE_CATEGORIES = 'Article Categories';
    const TAG_TREATMENTS = 'Treatments';
    const TAG_LANGUAGES = 'Languages';
    const TAG_AGE_GROUPS = 'Age Groups';

    const MAX_DEPTH = 1;

    const ENTITY_TYPES = [
        'article' => Article::class,
        'content' => Content::class,
        'event' => Event::class,
        'investor' => Investor::class,
        'job' => Job::class,
        'organization' => Organization::class,
        'person' => Person::class,
    ];

    /**
     * Relationships
     */
    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tag::class, 'parent_id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Tag::class, 'parent_id');
    }

    public function articles(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Article::class, 'taggable')->withTimestamps();
    }

    public function content(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Content::class, 'taggable')->withTimestamps();
    }

    public function events(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Event::class, 'taggable')->withTimestamps();
    }

    public function investors(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Investor::class, 'taggable')->withTimestamps();
    }

    public function jobs(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Job::class, 'taggable')->withTimestamps();
    }

    public function listings(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Listing::class, 'taggable')->withTimestamps();
    }

    public function organizations(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Organization::class, 'taggable')->withTimestamps();
    }

    public function people(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Person::class, 'taggable')->withTimestamps();
    }

    public function tagSets(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(TagSet::class)->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeFirstLevel($query)
    {
        $query->whereNull('parent_id');
    }

    public function scopeCanHaveKids($query) {
        $query->where('depth', '<', self::MAX_DEPTH);
    }

    /**
     * Mutators
     */
    public function setNameAttribute($value) {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::of($value)->slug('-');
    }

    /**
     * Attributes
     */
    public function getColorBadgeAttribute(): string
    {
        return match($this->name) {
            Article::TAG_TOP_INSIGHTS => 'bg-quaternary text-white',
            default => 'text-dark bg-gray-medium'
        };
    }

}
