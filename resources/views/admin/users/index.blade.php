@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <h1 class="text-primary me-5">Users</h1>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('admin.users.create') }}" class="btn btn-quaternary">Create User</a>
                @can('admin control')
                    <form action="{{ route('admin.users.bulk-delete-spam') }}"
                          method="POST"
                          class="d-inline"
                          onsubmit="return confirm('Delete all users that match the spam filter (no roles, unverified, no associated data)? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete spam users</button>
                    </form>
                @endcan
            </div>
        </div>

        @include('admin.includes.status-messages')

        <div class="mb-5">
            <livewire:admin.users.index />
        </div>

        <hr class="border-primary">

        <div class="my-5">
            <h2 class="h4">Roles &amp; Permissions</h2>
            <livewire:admin.users.roles-list />
        </div>

        @can('admin control')
            <div class="my-5">
                <h2 class="h4">About Page Order</h2>
                <livewire:admin.users.about-page-order />
            </div>
        @endcan

    </div>
@endsection
