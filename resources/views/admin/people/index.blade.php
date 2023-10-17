@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">People</h1>

        @include('admin.includes.status-messages')

        <livewire:entity-index.people.index  :showRouteRootPrefix="$showRouteRootPrefix" />
    </div>
@endsection

