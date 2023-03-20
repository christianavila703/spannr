@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="d-lg-flex justify-content-between align-items-center">
            <h1 class="text-primary mb-0">Spannr Job Applications</h1>
            <a href="{{ route('admin.recruiting.index') }}" class="btn btn-tertiary my-2">View Job Postings</a>
        </div>
        <livewire:admin.recruiting.job-applications-list />
    </div>

@endsection

@push('end')
    @vite('resources/js/recruiting.js')
@endpush
