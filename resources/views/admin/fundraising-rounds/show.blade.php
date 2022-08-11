@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:breadcrumbs>{!! $fundraisingRound->breadcrumb !!}</x-slot:breadcrumbs>
        <x-slot:buttons>
            <a href="{{ route('admin.fundraising-rounds.edit', $fundraisingRound->id) }}" class="btn btn-info">Edit</a>
        </x-slot:buttons>
        <x-slot:title>{{ $fundraisingRound->name }}</x-slot:title>
        @include('admin.fundraising-rounds.show-data')
    </x-entities.show>

@endsection
