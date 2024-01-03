@extends('layouts.user')

@section('content')
    <div class="container">
        <p class="lead mb-4">Hey {{ $user->name }}, welcome back!</p>

        @include('admin.includes.status-messages')

        <div class="row">
            @can('manage own jobs')
                <div class="mb-5 col-12 col-md-6 col-lg-4">
                    @include('users.dashboard-cards.manage-own-jobs')
                </div>
            @endcan

            <div class="mb-5 col-12 col-md-6 col-lg-4">
                @include('users.dashboard-cards.user-details')
            </div>

            @can('admin control')
                <div class="mb-5 col-12 col-md-6 col-lg-4">
                    <div class="card card-body">
                        <h2 class="h3">Admin Links</h2>
                        <div class="d-flex fs-5">
                            <ul class="list-group list-group-flush border-top-0 me-4">
                                <li class="list-group-item">
                                    <a href="{{ route('admin.users.index') }}">Users</a>
                                </li>
                                <li class="list-group-item">
                                    <a href="/horizon">Horizon</a>
                                </li>
                                <li class="list-group-item">
                                    <a href="/log-viewer">Log Viewer</a>
                                </li>
                            </ul>
                            <ul class="list-group list-group-flush border-top-0">
                                <li class="list-group-item">
                                    <a href="{{ route('admin.activity-log.index') }}">Activity Log</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endcan

            @can('edit profile')
                <div class="mb-5 col-12">
                    <div class="card card-body">
                        <h3>Profile</h3>
                        <livewire:users.user-profile :user="$user" />
                    </div>
                </div>
            @endcan
        </div>

    </div>
@endsection

