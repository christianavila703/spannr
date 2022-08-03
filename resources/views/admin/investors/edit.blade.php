@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.investors.show', $investor->slug) }}" backLabel="{{ $investor->name }}">
        <x-slot:title>Edit {{ $investor->name }}</x-slot:title>
        @include('admin.investors.form', [
            'route' => route('admin.investors.update', $investor->id),
            'method' => 'PUT'
        ])
    </x-entities.create>

@endsection
