@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.articles.index') }}" backLabel="Articles List" class="has-long-form" headingClass="h3">
        <x-slot:title>Edit {{ $article->name }}</x-slot:title>
        <div id="long-form-container">
            @include('admin.articles.form', [
                'route' => route('admin.articles.update', $article->id),
                'method' => 'PUT'
            ])
        </div>
        <div id="image-controls">
            <a id="view-post" href="{{ $article->url }}" class="btn btn-sm btn-quaternary">View Post</a>
            <div>
                <livewire:admin.has-media.featured-image :model="$article" />
                <a href="#upload-image" class="small">Set new featured image</a>
            </div>
            <div class="mt-4">
                <p class="h6">Images</p>
                <livewire:admin.has-media.post-images-widget :model="$article" />
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
                <livewire:admin.has-media.upload-featured-image :model="$article" />
            </div>
            <div class="mt-4">
                <livewire:admin.has-media.upload-post-image :model="$article" />
            </div>
        </div>
    </x-entities.create>

@endsection
