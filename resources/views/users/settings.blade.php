@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-primary">Settings</h1>

        @include('admin.includes.status-messages')

        @can('edit profile')
            <div class="mb-5">
                <h3>Profile</h3>
                <livewire:users.user-profile :user="$user" />
            </div>
        @endcan

        <div class="mb-5 max-width-600">
            @include('users._password')
        </div>

        @can('admin control')
            <div class="mb-5">
                @include('users._api-keys')
            </div>
        @endcan
    </div>
@endsection

