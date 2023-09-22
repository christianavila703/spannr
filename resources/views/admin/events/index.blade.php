@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Events</h1>

        @include('admin.includes.status-messages')

        <livewire:entity-index.events.index />
    </div>
@endsection
