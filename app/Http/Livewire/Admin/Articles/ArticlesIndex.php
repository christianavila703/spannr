<?php

namespace App\Http\Livewire\Admin\Articles;

use App\Http\Livewire\Traits\WithBulkActions;
use App\Http\Livewire\Traits\WithCachedRows;
use App\Http\Livewire\Traits\WithPerPagePagination;
use App\Http\Livewire\Traits\WithSorting;
use App\Models\Article;
use App\Models\Tag;
use Livewire\Component;

class ArticlesIndex extends Component
{
    use WithPerPagePagination, WithBulkActions, WithCachedRows, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public $filters = [
        'search' => '',
        'selected-statuses' => []
    ];

    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function resetFilters() {
        $this->reset('filters');
    }

    public function setTopInsight() {
        $articleIds = $this->selected;
        $tag = Tag::where('name', Article::TAG_TOP_INSIGHTS)->firstOrFail();

        foreach ($articleIds as $id) {
            $article = Article::findOrFail($id);
            $article->tags()->syncWithoutDetaching($tag->id);
        }

        $this->selected = [];
        $this->selectPage = false;
    }

    public function getRowsQueryProperty()
    {
        $query = Article::orderByDesc('date')
                ->with(['authors', 'tags'])
                ->when($this->filters['search'], function($query, $search) {
                    return $query
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('excerpt', 'like', '%' . $search . '%')
                        ->orWhere('date', 'like', '%' . $search . '%')
                        ->orWhere('content', 'like', '%' . $search . '%')
                        ->orWhere('status', 'like', '%' . $search . '%')
                        ->orWhereHas('authors', function($query) use ($search) {
                            $query->where('name', 'like', '%' . $search . '%');
                        });
                })
                ->when($this->filters['selected-statuses'], function($query, $selectedValues) {
                    return $query->whereIn('status', $selectedValues);
                });

        return $this->applySorting($query);
    }

    public function getRowsProperty()
    {
        return $this->cache(function () {
            return $this->applyPagination($this->rowsQuery);
        });
    }

    public function render()
    {
        return view('livewire.admin.articles.articles-index', [
            'articles' => $this->rows
        ]);
    }
}
