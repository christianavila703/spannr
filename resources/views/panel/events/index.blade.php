@extends('layouts.panel')

@section('panel')
    <div class="container-fluid">
        <h1 class="text-primary">Events</h1>
        <livewire:entity-index.events.index  :showRouteRootPrefix="$showRouteRootPrefix" />
    </div>
@endsection
