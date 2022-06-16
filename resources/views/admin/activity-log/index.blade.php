@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Activity Log</h1>

        @include('admin.includes.status-messages')

        <livewire:activity-log />

    </div>
@endsection
