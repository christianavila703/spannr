@extends('layouts.panel')

@section('panel')
    <div class="container-fluid">
        <h1 class="text-primary">Investors</h1>
        <livewire:entity-index.investors.index  :showRouteRootPrefix="$showRouteRootPrefix" />
    </div>
@endsection

