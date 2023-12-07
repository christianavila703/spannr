@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-lg-flex justify-content-between">
            <h1 class="text-primary">Imported Listing</h1>
        </div>

        <livewire:admin.imports.listings.process-listing-import :importResult="$importResult" />
    </div>
@endsection
