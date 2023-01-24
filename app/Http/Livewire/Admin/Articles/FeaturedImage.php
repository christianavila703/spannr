<?php

namespace App\Http\Livewire\Admin\Articles;

use App\Models\Article;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FeaturedImage extends Component
{

    public Article $article;

    public function render()
    {
        return view('livewire.admin.articles.featured-image', [
            'image' => Media::whereHas('model', function($query) {
                $query->where('id', $this->article->id)
                    ->where('collection_name', 'featured');
            })->first()
        ]);
    }
}
