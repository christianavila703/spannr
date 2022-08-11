@extends('layouts.app')

@section('content')

    <x-entities.show class="tag-page">
        <x-slot:breadcrumbs>{!! $tag->breadcrumb !!}</x-slot:breadcrumbs>
        <x-slot:title>{{ $tag->name }}</x-slot:title>
        <x-slot:buttons>
            <a href="{{ route('admin.tags.edit', $tag->id) }}" class="btn btn-info me-3">Edit</a>
        </x-slot:buttons>
        @include('admin.tags.show-data')
    </x-entities.show>

@endsection
