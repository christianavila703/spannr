@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ $glossary->url }}" backLabel="Glossary">
        <x-slot:title>Edit {{ $glossary->name }}</x-slot:title>
        @include('admin.glossary.form', [
            'route' => route('admin.glossary.update', $glossary->id),
            'method' => 'PUT'
        ])
    </x-entities.create>

@endsection
