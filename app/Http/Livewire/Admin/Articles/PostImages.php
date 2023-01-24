<?php

namespace App\Http\Livewire\Admin\Articles;

use App\Models\Article;
use Livewire\Component;
use App\Http\Livewire\DataTable\WithBulkActions;
use App\Http\Livewire\DataTable\WithCachedRows;
use App\Http\Livewire\DataTable\WithPerPagePagination;
use App\Http\Livewire\DataTable\WithSorting;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PostImages extends Component
{
    use WithPerPagePagination, WithBulkActions, WithCachedRows, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public Article $article;

    public $filters = [
        'search' => ''
    ];

    public $deletableImage = null;

    public function deleteConfirmation($imageId) {
        $this->deletableImage = $imageId;
    }

    public function deleteItem($imageId) {
        Media::find($imageId)->delete();
    }

    public function resetDeletable() {
        $this->reset('deletableImage');
    }

    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function resetFilters() {
        $this->reset('filters');
    }

    public function resetFilter($filter) {
        $this->filters[$filter] = null;
    }

    public function getRowsQueryProperty()
    {
        $query = Media::query()->whereHas('model', function($query) {
            $query->where('id', $this->article->id)
            ->where('collection_name', 'images');
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
        return view('livewire.admin.articles.post-images', [
            'images' => $this->rows
        ]);
    }
}
