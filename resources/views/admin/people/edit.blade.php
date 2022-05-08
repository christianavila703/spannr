@extends('layouts.app')

@section('content')

    <x-entities.create>
        <x-slot:title>Edit {{ $person->name }}</x-slot:title>
        @include('admin.people.form', [
            'route' => route('people.update', $person->id),
            'method' => 'PUT'
        ])
    </x-entities.create>

@endsection
