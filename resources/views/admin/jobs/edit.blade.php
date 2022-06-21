@extends('layouts.app')

@section('content')

    <x-entities.create>
        <x-slot:title>Edit {{ $job->name }}</x-slot:title>
        @include('admin.jobs.form', [
            'route' => route('jobs.update', $job->id),
            'method' => 'PUT'
        ])
    </x-entities.create>

@endsection
