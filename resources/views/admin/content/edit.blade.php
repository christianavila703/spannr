@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.content.index') }}" backLabel="Content" class="has-long-form">
        <x-slot:title>Add Content</x-slot:title>
        <div id="long-form-container">
            @include('admin.content.form', [
                'route' => route('admin.content.update', $content->id),
                'method' => 'PUT'
            ])
        </div>

        <div id="image-controls">
            <a id="view-post" href="{{ $content->link }}" class="btn btn-sm btn-quaternary">View Content</a>
            <div>
                <livewire:admin.has-media.featured-image :model="$content" />
                <a href="#upload-image" class="small">Set new featured image</a>
            </div>
            <div class="mt-4">
                <h3 class="h6">Images</h3>
                <livewire:admin.has-media.post-images-widget :model="$content" />
            </div>
            <div>
                <a href="#upload-image" class="btn btn-sm btn-quintary mx-auto">
                    <i class="fa-sharp fa-solid fa-image fa-2x mt-1"></i>
                    <span class="d-block">Upload Image</span>
                </a>
            </div>
        </div>

        <div id="upload-image" class="mt-5">
            <div class="max-width-600">
                <livewire:admin.has-media.upload-featured-image :model="$content" />
            </div>
            <div class="mt-4">
                <livewire:admin.has-media.upload-post-image :model="$content" />
            </div>
        </div>
    </x-entities.create>

@endsection

@push('end')

@endpush
