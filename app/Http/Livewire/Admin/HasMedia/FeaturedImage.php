<?php

namespace App\Http\Livewire\Admin\HasMedia;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FeaturedImage extends Component
{
    public Model $model;

    public function render()
    {
        return view('livewire.admin.has-media.featured-image', [
            'image' => Media::whereHas('model', function($query) {
                $query->where('id', $this->model->id)
                    ->where('collection_name', 'featured');
            })->first()
        ]);
    }
}
