@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Organizations</h1>

        @include('admin.includes.status-messages')

        <livewire:organizations.index />
    </div>
@endsection

