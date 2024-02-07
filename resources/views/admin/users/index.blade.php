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
