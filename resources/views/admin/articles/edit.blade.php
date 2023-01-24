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
            <h4 class="text-decoration-underline">
                <a href="{{ route('admin.articles.media', $article->id) }}">Images</a>
            </h4>
            <div class="mb-4">
                <livewire:admin.articles.post-images-widget :article="$article" />
            </div>
            <div>
                <a href="#upload-image" class="btn btn-sm btn-quintary mx-auto">
                    <i class="fa-sharp fa-solid fa-image fa-2x mt-1"></i>
                    <span class="d-block">Upload Image</span>
                </a>
            </div>
        </div>
        <div id="upload-image" class="mt-5">
            <livewire:admin.articles.upload-post-image :article="$article" />
        </div>
    </x-entities.create>

@endsection
