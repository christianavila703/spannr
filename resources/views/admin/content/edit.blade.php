@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.content.index') }}" backLabel="Content" class="has-long-form">
        <x-slot:title>Edit Content</x-slot:title>
        <div id="long-form-container">
            @include('admin.content.form', [
                'route' => route('admin.content.update', $content->id),
                'method' => 'PUT'
            ])
        </div>

        <div id="image-controls">
            <a id="view-post" href="{{ $content->parent_id ? $content->link : route('public.content.page', $content->slug) }}" class="btn btn-sm btn-quaternary">View Content</a>
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

        <div id="upload-image" class="my-5 border p-4">
            <div class="row">
                <div class="col-12 col-xl-6 mb-4 mb-xl-0 ">
                    <livewire:admin.has-media.upload-featured-image :model="$content" />
                </div>
                <div class="col-12 col-xl-6 ps-xl-5">
                    <livewire:admin.has-media.upload-post-image :model="$content" />
                </div>
            </div>
        </div>

        @if ($content->children)
            <div id="content-children" class="my-5 border p-4">
                <h2 class="h4">Manage Children</h2>
                <livewire:admin.content.manage-child-order :content="$content" />
            </div>
        @endif
    </x-entities.create>

@endsection

@push('end')

@endpush
