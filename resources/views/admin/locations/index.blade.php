@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Locations</h1>

        @include('admin.includes.status-messages')

        <livewire:entity-index.locations.index  :showRouteRootPrefix="$showRouteRootPrefix" />
    </div>
@endsection

