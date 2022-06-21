@extends('layouts.app')

@section('content')

    <x-entities.create>
        <x-slot:title>Add Job</x-slot:title>
        @include('admin.jobs.form', ['route' => route('jobs.store')])
    </x-entities.create>

@endsection
