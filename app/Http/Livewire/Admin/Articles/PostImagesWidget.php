<?php

namespace App\Http\Livewire\Admin\Articles;

use App\Models\Article;
use Livewire\Component;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PostImagesWidget extends Component
{
    public Article $article;

    public function render()
    {
        return view('livewire.admin.articles.post-images-widget', [
            'images' => Media::orderByDesc('created_at')->whereHas('model', function($query) {
                $query->where('id', $this->article->id)->where('collection_name', 'images');
            })->get()
        ]);
    }
}
