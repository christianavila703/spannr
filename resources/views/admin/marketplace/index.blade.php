@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Marketplace Listings</h1>

        @include('admin.includes.status-messages')

        <livewire:admin.marketplace.index />
    </div>
@endsection

