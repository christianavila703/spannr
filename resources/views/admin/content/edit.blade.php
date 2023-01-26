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
                <livewire:admin.content.featured-image :content="$content" />
                <a href="#upload-image">Set new featured image</a>
            </div>
        </div>

        <div id="upload-image" class="mt-5">
            <div class="max-width-600">
                <livewire:admin.content.upload-featured-image :content="$content" />
            </div>
        </div>
    </x-entities.create>

@endsection

@push('end')

@endpush
