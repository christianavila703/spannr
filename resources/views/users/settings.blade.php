@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-primary">Settings</h1>

        @include('admin.includes.status-messages')

        <div class="mb-5 max-width-600">
            @include('users._password')
        </div>

        <div class="mb-5">
            @include('users._api-keys')
        </div>
    </div>
@endsection

