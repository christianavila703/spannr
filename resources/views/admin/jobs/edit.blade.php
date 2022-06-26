@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('jobs.show', $job->slug) }}" backLabel="{{ $job->name }}">
        <x-slot:title>Edit {{ $job->name }}</x-slot:title>
        @include('admin.jobs.form', [
            'route' => route('jobs.update', $job->id),
            'method' => 'PUT'
        ])
    </x-entities.create>

@endsection
