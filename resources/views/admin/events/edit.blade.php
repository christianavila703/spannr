@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('events.show', $event->slug) }}" backLabel="{{ $event->name }}">
        <x-slot:title>Edit Event</x-slot:title>
        @include('admin.events.form', [
            'route' => route('events.update', $event->id),
            'method' => 'PUT'
        ])
    </x-entities.create>

@endsection
