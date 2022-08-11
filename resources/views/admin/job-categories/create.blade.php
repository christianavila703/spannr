@extends('layouts.app')

@section('content')

    <x-entities.create backUrl="{{ route('admin.jobs.index') }}" backLabel="Jobs">
        <x-slot:title>Add Job Category</x-slot:title>
        <div class="row">
            <div class="col-12 col-md-8 col-lg-6">
                @include('admin.job-categories.form', ['route' => route('admin.job-categories.store')])
            </div>
        </div>

    </x-entities.create>

@endsection
