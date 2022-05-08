@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:title>{{ $organization->name }}</x-slot:title>
        <x-slot:buttons>
            <a href="{{ route('organizations.edit', $organization->id) }}" class="btn btn-info">Edit</a>
        </x-slot:buttons>
        @include('admin.organizations.show-data')
    </x-entities.show>

@endsection
