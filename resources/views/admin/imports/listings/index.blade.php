@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-lg-flex justify-content-between">
            <h1 class="text-primary">Imported Listings</h1>
            <div>
                <a href="{{ route('admin.import.listings.create') }}" class="btn btn-primary">New Import</a>
            </div>
        </div>

        @include('admin.includes.status-messages')

        <livewire:admin.imports.listings.imported-listings />
    </div>
@endsection
