@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between">
            <h1 class="text-primary me-5">Users</h1>
            <div>
                <a href="{{ route('admin.users.create') }}" class="btn btn-quaternary">Create User</a>
            </div>
        </div>

        @include('admin.includes.status-messages')

        <livewire:admin.users.index />

        <livewire:admin.users.roles-list />
    </div>
@endsection
