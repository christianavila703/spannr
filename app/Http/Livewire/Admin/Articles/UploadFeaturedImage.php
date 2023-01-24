<?php

namespace App\Http\Livewire\Admin\Articles;

use App\Models\Article;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadFeaturedImage extends Component
{
    use WithFileUploads;

    public Article $article;

    public $mediaComponentNames = ['featuredImage'];

    public $featuredImage;

    public $message = '';

    public function submit()
    {
        $this->validate([
            'featuredImage' => 'image|max:1024', // 1MB Max
        ]);

        $this->article
            ->addMedia($this->featuredImage->getRealPath())
            ->setName($this->article->name)
            ->usingFileName($this->article->slug . '.' . $this->featuredImage->extension())
            ->toMediaCollection('featured');

        $this->message = 'Featured image was set :)';
    }

    public function render()
    {
        return view('livewire.admin.articles.upload-featured-image');
    }
}
