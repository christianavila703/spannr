<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Storage;

trait HasImage {
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return '/storage/images/' . $this->modelName . '/' . $this->image;
        } elseif ($this->blankImage) {
            return $this->blankImage;
        } else {
            return '';
        }
    }

    public function getImageFullUrlAttribute(): string
    {
        if ($this->image) {
            return url('/') . '/storage/images/' . $this->modelName . '/' . $this->image;
        } elseif ($this->blankImage) {
            return url('/') . $this->blankImage;
        } else {
            return '';
        }
    }

    public function storeImage($file) {
        $fileName = $this->slug . "." . $file->getClientOriginalExtension();
        Storage::putFileAs($this->imageUrlStoragePath, $file, $fileName);
        $this->image = $fileName;
        $this->save();
    }

    public function removeImageFile() {
        if ($this->image) {
            Storage::delete($this->imageUrlStoragePath . DIRECTORY_SEPARATOR . $this->image);
        }
    }
}
