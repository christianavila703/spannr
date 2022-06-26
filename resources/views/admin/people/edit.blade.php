@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('people.show', $person->slug) }}" backLabel="{{ $person->name }}">
        <x-slot:title>Edit {{ $person->name }}</x-slot:title>
        @include('admin.people.form', [
            'route' => route('people.update', $person->id),
            'method' => 'PUT'
        ])
    </x-entities.create>

@endsection
