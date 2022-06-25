@extends('layouts.app')

@section('content')

    <x-entities.create>
        <x-slot:title>Add Job Category</x-slot:title>
        @include('admin.job-categories.form', ['route' => route('job-categories.store')])
    </x-entities.create>

@endsection
