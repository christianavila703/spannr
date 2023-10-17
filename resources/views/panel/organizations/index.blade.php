@extends('layouts.panel')

@section('panel')
    <div class="container-fluid">
        <h1 class="text-primary">Organizations</h1>
        <livewire:entity-index.organizations.index :showRouteRootPrefix="$showRouteRootPrefix" />
    </div>
@endsection

