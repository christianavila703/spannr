@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Locations</h1>

        @include('admin.includes.status-messages')

        <livewire:admin.locations.index/>
    </div>
@endsection

