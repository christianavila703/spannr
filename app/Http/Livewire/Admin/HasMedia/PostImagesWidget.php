<?php

namespace App\Http\Livewire\Admin\HasMedia;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PostImagesWidget extends Component
{
    public Model $model;

    public function render()
    {
        return view('livewire.admin.has-media.post-images-widget', [
            'images' => Media::orderByDesc('created_at')->whereHas('model', function($query) {
                $query->where('id', $this->model->id)->where('collection_name', 'images');
            })->get()
        ]);
    }
}
