@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.articles.index') }}" backLabel="Articles" class="has-long-form">
        <x-slot:title>Create Article</x-slot:title>
        <div id="long-form-container">
            @include('admin.articles.form', ['route' => route('admin.articles.store')])
        </div>
    </x-entities.create>

@endsection
