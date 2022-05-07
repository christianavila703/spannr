@extends('layouts.app')

@section('content')

<x-entities.create>
    <x-slot:title>Edit {{ $organization->name }}</x-slot:title>
    @include('admin.organizations.form', [
        'route' => route('organizations.update', $organization->id),
        'method' => 'PUT'
    ])
</x-entities.create>

@endsection
