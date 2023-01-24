<?php

namespace App\Http\Livewire\Admin\Articles;

use App\Models\Article;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\Image\Image;

class UploadPostImage extends Component
{
    use WithFileUploads;

    public Article $article;

    public $mediaComponentNames = ['image'];

    public $image;
    public $description;

    public $message = '';

    public function submit()
    {
        $this->validate([
            'image' => 'required|image|max:1024',
            'description' => 'required|string'
        ]);

        $this->article
            ->addMedia($this->image->getRealPath())
            ->setName($this->description)
            ->withCustomProperties($this->getCustomImageProperties($this->image->getRealPath()))
            ->usingFileName(Str::slug(Str::words($this->description, 5)) . '.' . $this->image->extension())
            ->toMediaCollection('images');

        $this->message = 'Image uploaded to article';
        $this->reset('image');
        $this->reset('description');
    }

    private function getCustomImageProperties($imagePath): array
    {
        $properties = [
            'height' => null,
            'width' => null,
            'post-height' => null,
            'post-width' => null,
        ];

        try {
            $image = Image::load($imagePath);

            $properties['height'] = $image->getHeight();
            $properties['width'] = $image->getWidth();
            $properties['post-height'] = $image->getHeight();
            $properties['post-width'] = $image->getWidth();

            if ($image->getWidth() > 600) {
                $image->width(600)->save();
                $properties['post-height'] = $image->getHeight();
                $properties['post-width'] = $image->getWidth();
            }

            return $properties;

        } catch (\Throwable $exception) {
            Log::critical('getDimensions Failure ' . $exception->getMessage());
            return $properties;
        }

    }

    private function getDimensions($imagePath): array
    {
        try {
            $image = Image::load($imagePath);

            if ($image->getWidth() > 600) {
                $image->width(600)->save();
            }

            return [$image->getWidth(), $image->getHeight()];

        } catch (\Throwable $exception) {
            Log::critical('getDimensions Failure ' . $exception->getMessage());
            return [null, null];
        }
    }

    public function render()
    {
        return view('livewire.admin.articles.upload-post-image');
    }
}
