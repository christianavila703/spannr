@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:title>{{ $job->name }}</x-slot:title>
        <x-slot:buttons>
            <a href="{{ route('jobs.edit', $job->id) }}" class="btn btn-info me-3">Edit</a>
        </x-slot:buttons>
        @include('admin.jobs.show-data')
    </x-entities.show>

@endsection
