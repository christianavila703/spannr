@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Jobs</h1>

        @include('admin.includes.status-messages')

        <div class="mt-4 mb-4">
            <a href="{{ route('admin.job-categories.index') }}" class="btn btn-info">Job Categories</a>
        </div>

        <livewire:entity-index.jobs.index />
    </div>
@endsection
