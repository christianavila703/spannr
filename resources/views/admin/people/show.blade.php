@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:title>{{ $person->name }}</x-slot:title>
        <x-slot:buttons>
            <a href="{{ route('people.edit', $person->id) }}" class="btn btn-info">Edit</a>
        </x-slot:buttons>
        @include('admin.people.show-data')
    </x-entities.show>

@endsection
