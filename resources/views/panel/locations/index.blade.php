@extends('layouts.panel')

@section('panel')
    <div class="container-fluid">
        <h1 class="text-primary">Locations</h1>
        <livewire:entity-index.locations.index :showRouteRootPrefix="$showRouteRootPrefix" />
    </div>
@endsection

