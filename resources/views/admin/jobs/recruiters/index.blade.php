@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="d-lg-flex justify-content-between align-items-center">
            <h1 class="text-primary mb-0">Spannr Jobs</h1>
            <a href="{{ route('admin.recruiting.applications') }}" class="btn btn-tertiary my-2">View Job Applications</a>
        </div>
        <livewire:admin.recruiting.jobs-list />
    </div>

@endsection

@push('end')
    @vite('resources/js/recruiting.js')
@endpush
