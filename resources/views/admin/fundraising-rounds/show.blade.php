@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:breadcrumbs>
            <x-nav.entity-breadcrumbs :breadcrumbs="$fundraisingRound->generateBreadcrumbs()"></x-nav.entity-breadcrumbs>
        </x-slot:breadcrumbs>
        <x-slot:buttons>
            @include('admin.entity-reviews.includes.entity-form', ['entity' => $fundraisingRound])

            @if($fundraisingRound->isLocked && $fundraisingRound->lockedById !== auth()->user()->id)
                <x-entities.locked-label :record="$fundraisingRound" />
            @else
                <a href="{{ route('admin.fundraising-rounds.edit', $fundraisingRound->id) }}" class="btn btn-info me-3">Edit</a>
                <form action="{{ route('admin.fundraising-rounds.destroy', $fundraisingRound->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger confirm-button">Delete</button>
                </form>
            @endif
        </x-slot:buttons>
        <x-slot:title>{{ $fundraisingRound->name }}</x-slot:title>
        <x-entities.data.fundraising-round :fundraisingRound="$fundraisingRound" showRouteRootPrefix="{{$showRouteRootPrefix}}"/>
    </x-entities.show>

@endsection
