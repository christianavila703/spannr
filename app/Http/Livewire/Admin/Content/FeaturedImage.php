<?php

namespace App\Http\Livewire\Admin\Content;

use App\Models\Content;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FeaturedImage extends Component
{
    public Content $content;

    public function render()
    {
        return view('livewire.admin.content.featured-image', [
            'image' => Media::whereHas('model', function($query) {
                $query->where('id', $this->content->id)
                    ->where('collection_name', 'featured');
            })->first()
        ]);
    }
}
