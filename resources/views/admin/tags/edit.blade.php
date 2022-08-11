@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.tags.show', $tag->slug) }}" backLabel="{{ $tag->name }}">
        <x-slot:title>Edit {{ $tag->name }}</x-slot:title>
        <div class="row">
            <div class="col-12 col-md-6 col-lg-5">
                @include('admin.tags.form', [
                    'route' => route('admin.tags.update', $tag->id),
                    'method' => 'PUT'
                ])
            </div>
        </div>
    </x-entities.create>

@endsection
