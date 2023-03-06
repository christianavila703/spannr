@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:breadcrumbs>{!! $fundraisingRound->breadcrumb !!}</x-slot:breadcrumbs>
        <x-slot:buttons>
            @include('admin.entity-reviews.includes.entity-form', ['entity' => $fundraisingRound])

            <a href="{{ route('admin.fundraising-rounds.edit', $fundraisingRound->id) }}" class="btn btn-info me-3">Edit</a>
            <form action="{{ route('admin.fundraising-rounds.destroy', $fundraisingRound->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger confirm-button">Delete</button>
            </form>
        </x-slot:buttons>
        <x-slot:title>{{ $fundraisingRound->name }}</x-slot:title>
        @include('admin.fundraising-rounds.show-data')
    </x-entities.show>

@endsection
