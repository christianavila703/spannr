@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.articles.edit', $article->id) }}" backLabel="Article Edit" headingClass="h3">
        <x-slot:title>Media for {{ $article->name }}</x-slot:title>
        <div class="d-flex flex-wrap align-items-center">
            <div class="col-12 col-md-6 col-lg-5">
                <livewire:admin.has-media.upload-featured-image :model="$article" />
            </div>
            <div class="col-12 col-md-5 col-lg-5 offset-md-1 offset-lg-2">
                <livewire:admin.has-media.featured-image :model="$article" />
            </div>
        </div>
        <hr class="dark my-5">
        <div>
            <div class="mb-4">
                <livewire:admin.has-media.upload-post-image :model="$article" />
            </div>
            <div>
                <livewire:admin.has-media.post-images :model="$article" />
            </div>
        </div>
    </x-entities.create>
@endsection
