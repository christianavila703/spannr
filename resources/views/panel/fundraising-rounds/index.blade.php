@extends('layouts.panel')

@section('panel')
    <div class="container-fluid">
        <h1 class="text-primary">Fundraising Rounds</h1>
        <livewire:entity-index.fundraising-rounds.index  :showRouteRootPrefix="$showRouteRootPrefix" />
    </div>
@endsection

