<?php

namespace App\Http\Livewire\Admin\HasMedia;

use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Livewire\WithFileUploads;

class UploadFeaturedImage extends Component
{
    use WithFileUploads;

    public Model $model;

    public $mediaComponentNames = ['featuredImage'];

    public $featuredImage;

    public $message = '';

    public $headingClass = 'h4 mb-3';

    public $imageRequirements;

    public function submit()
    {
        $this->validate([
            'featuredImage' => 'image|max:1024', // 1MB Max
        ]);

        $this->model
            ->addMedia($this->featuredImage->getRealPath())
            ->setName($this->model->name)
            ->usingFileName($this->model->slug . '.' . $this->featuredImage->extension())
            ->toMediaCollection('featured');

        $this->message = 'Featured image was set :)';
    }

    public function render()
    {
        return view('livewire.admin.has-media.upload-featured-image');
    }
}
