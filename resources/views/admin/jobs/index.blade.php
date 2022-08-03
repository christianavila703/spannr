@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Jobs</h1>

        @include('admin.includes.status-messages')

        <livewire:jobs.index />

        <div class="mt-3">
            <a href="{{ route('admin.job-categories.create') }}" class="btn btn-sm btn-outline-tertiary">Add Job Category</a>
        </div>
    </div>
@endsection
