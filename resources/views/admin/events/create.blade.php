@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('events.index') }}" backLabel="Events">
        <x-slot:title>Add Event</x-slot:title>
        @include('admin.events.form', ['route' => route('events.store')])
    </x-entities.create>

@endsection
