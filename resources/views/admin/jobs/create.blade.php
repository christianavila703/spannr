@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.jobs.index') }}" backLabel="Jobs">
        <x-slot:title>Add Job</x-slot:title>
        @include('admin.jobs.form', ['route' => route('admin.jobs.store')])
    </x-entities.create>

@endsection
