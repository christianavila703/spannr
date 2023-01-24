@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.articles.edit', $article->id) }}" backLabel="Article Edit" headingClass="h3">
        <x-slot:title>Media for {{ $article->name }}</x-slot:title>
        <div class="d-flex flex-wrap align-items-center">
            <div class="col-12 col-md-6 col-lg-5">
                <livewire:admin.articles.upload-featured-image :article="$article" />
            </div>
            <div class="col-12 col-md-5 col-lg-5 offset-md-1 offset-lg-2">
                <livewire:admin.articles.featured-image :article="$article" />
            </div>
        </div>
        <hr class="dark my-5">
        <div>
            <div class="mb-4">
                <livewire:admin.articles.upload-post-image :article="$article" />
            </div>
            <div>
                <livewire:admin.articles.post-images :article="$article" />
            </div>
        </div>
    </x-entities.create>
@endsection
