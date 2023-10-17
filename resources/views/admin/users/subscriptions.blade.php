@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between">
            <h1 class="text-primary me-5">Subscriptions</h1>
        </div>

        @include('admin.includes.status-messages')

        <livewire:admin.users.subscriptions />
    </div>
@endsection
