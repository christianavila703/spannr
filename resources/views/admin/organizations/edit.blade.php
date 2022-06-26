@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('organizations.show', $organization->slug) }}" backLabel="{{ $organization->name }}">
        <x-slot:title>Edit {{ $organization->name }}</x-slot:title>
        @include('admin.organizations.form', [
            'route' => route('organizations.update', $organization->id),
            'method' => 'PUT'
        ])
    </x-entities.create>

@endsection
