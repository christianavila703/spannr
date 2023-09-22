@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="text-primary">Fundraising Rounds</h1>

        @include('admin.includes.status-messages')

        <livewire:entity-index.fundraising-rounds.index />
    </div>
@endsection

