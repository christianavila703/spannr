@extends('layouts.app')

@section('content')

    <x-entities.create>
        <x-slot:title>Edit {{ $event->name }}</x-slot:title>
        @include('admin.events.form', [
            'route' => route('events.update', $event->id),
            'method' => 'PUT'
        ])
    </x-entities.create>

@endsection
