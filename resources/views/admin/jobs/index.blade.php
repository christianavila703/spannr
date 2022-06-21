@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Jobs</h1>

        @include('admin.includes.status-messages')

        <livewire:jobs.index />
    </div>
@endsection
