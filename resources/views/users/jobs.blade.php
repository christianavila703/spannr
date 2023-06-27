@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-primary">Posted Jobs</h1>

        @include('admin.includes.status-messages')

        <div class="mb-5">
            <livewire:users.jobs />
        </div>
    </div>
@endsection

