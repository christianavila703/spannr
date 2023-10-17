@extends('layouts.panel')

@section('panel')
    <div class="container-fluid">
        <h1 class="text-primary">People</h1>
        <livewire:entity-index.people.index  :showRouteRootPrefix="$showRouteRootPrefix" />
    </div>
@endsection

