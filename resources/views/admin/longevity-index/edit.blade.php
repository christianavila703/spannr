@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="my-3">
            <a href="{{ route('admin.longevity-index.recalculate', $marketSnapshot->id) }}" class="btn btn-quaternary">Recalculate Market Snapshot</a>
        </div>

        <livewire:longevity-index.edit-market-snapshot :marketSnapshot="$marketSnapshot" />

        @include('admin.longevity-index._nav-group')
    </div>
@endsection
