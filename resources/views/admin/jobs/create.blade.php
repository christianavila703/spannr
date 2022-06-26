@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('jobs.index') }}" backLabel="Jobs">
        <x-slot:title>Add Job</x-slot:title>
        @include('admin.jobs.form', ['route' => route('jobs.store')])
    </x-entities.create>

@endsection
