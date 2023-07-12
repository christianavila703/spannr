@extends('layouts.app')

@section('content')

    <x-entities.show class="tag-page">
        <x-slot:breadcrumbs>{!! $tag->breadcrumb !!}</x-slot:breadcrumbs>
        <x-slot:title>{{ $tag->name }}</x-slot:title>
        <x-slot:buttons>
            <a href="{{ route('admin.tags.edit', $tag->id) }}" class="btn btn-info me-3">Edit</a>
            <form method="POST" action="{{ route('admin.tags.destroy', $tag->id ) }}">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger confirm-button">
                    <i class="fa-duotone fa-trash-can me-1"></i>Delete
                </button>
            </form>
        </x-slot:buttons>
        @include('admin.tags.show-data')
    </x-entities.show>

@endsection
