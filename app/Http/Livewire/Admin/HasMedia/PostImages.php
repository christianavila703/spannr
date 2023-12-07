<?php

namespace App\Http\Livewire\Admin\HasMedia;

use App\Http\Livewire\Traits\WithBulkActions;
use App\Http\Livewire\Traits\WithCachedRows;
use App\Http\Livewire\Traits\WithPerPagePagination;
use App\Http\Livewire\Traits\WithSorting;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PostImages extends Component
{
    use WithPerPagePagination, WithBulkActions, WithCachedRows, WithSorting;

    protected $paginationTheme = 'bootstrap';

    public Model $model;

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
            $query->where('id', $this->model->id)
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
        return view('livewire.admin.has-media.post-images', [
            'images' => $this->rows
        ]);
    }
}
