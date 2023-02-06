@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Organizations</h1>

        @include('admin.includes.status-messages')

        <div class="mt-4 mb-4">
            <a href="{{ route('admin.organizations.stock.create') }}" class="btn btn-info">Add Stock to Organization</a>
        </div>

        <livewire:organizations.index/>
    </div>
@endsection

