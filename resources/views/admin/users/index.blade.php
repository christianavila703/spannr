@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-primary">Users</h1>

        @include('admin.includes.status-messages')

        <livewire:users.index />

        <livewire:users.roles-list />
    </div>
@endsection
