@extends('layouts.panel')

@section('panel')
    <div class="container-fluid">
        <h1 class="text-primary">Jobs</h1>
        <livewire:entity-index.jobs.index  :showRouteRootPrefix="$showRouteRootPrefix" />
    </div>
@endsection
