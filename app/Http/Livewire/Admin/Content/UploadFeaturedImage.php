<?php

namespace App\Http\Livewire\Admin\Content;

use App\Models\Article;
use App\Models\Content;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadFeaturedImage extends Component
{
    use WithFileUploads;

    public Content $content;

    public $mediaComponentNames = ['featuredImage'];

    public $featuredImage;

    public $message = '';

    public function submit()
    {
        $this->validate([
            'featuredImage' => 'image|max:1024', // 1MB Max
        ]);

        $this->content
            ->addMedia($this->featuredImage->getRealPath())
            ->setName($this->content->name)
            ->usingFileName($this->content->slug . '.' . $this->featuredImage->extension())
            ->toMediaCollection('featured');

        $this->message = 'Featured image was set :)';
    }

    public function render()
    {
        return view('livewire.admin.content.upload-featured-image');
    }
}
