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

    protected $guarded = [];

    private string $searchableRouteName = 'admin.tags.show';
    private string $searchableRouteField = 'slug';
    private string $searchableIcon = 'fa-duotone fa-tag';

    private string $modelName = 'tags';

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

    public function organizations(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Organization::class, 'taggable')->withTimestamps();
    }

    public function people(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphedByMany(Person::class, 'taggable')->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeFirstLevel($query)
    {
        $query->whereNull('parent_id');
    }

    public function scopeSecondLevel($query)
    {
        $query->where('depth', 1);
    }

    public function scopeThirdLevel($query)
    {
        $query->where('depth', 2);
    }

    public function scopeCanHaveKids($query) {
        $query->where('depth', '<', 2);
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
