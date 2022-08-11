@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Jobs</h1>

        @include('admin.includes.status-messages')

        <livewire:jobs.index />

        <div class="d-flex flex-wrap">
            <div id="job-categories" class="mt-5 max-width-600 me-5 flex-grow-1">
                <h2 class="text-quaternary mb-0 me-3">Job Categories</h2>
                <livewire:admin.jobs.job-categories-index />
            </div>
            <div class="max-width-600 flex-grow-1 mt-5 ps-xl-5">
                <h2 class="text-tertiary mb-0 me-3">Add Job Category</h2>
                @include('admin.job-categories.form', ['route' => route('admin.job-categories.store')])
            </div>
        </div>
    </div>
@endsection
