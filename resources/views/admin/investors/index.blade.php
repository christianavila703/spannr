@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Investors</h1>

        @include('admin.includes.status-messages')

        <livewire:entity-index.investors.index  :showRouteRootPrefix="$showRouteRootPrefix" />
    </div>
@endsection

