@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:breadcrumbs>
            <x-nav.entity-breadcrumbs :breadcrumbs="$job->generateBreadcrumbs()"></x-nav.entity-breadcrumbs>
        </x-slot:breadcrumbs>
        <x-slot:buttons>
            @if($job->isLocked && $job->lockedById !== auth()->user()->id)
                <x-entities.locked-label :record="$job" />
            @else
                <a href="{{ route('admin.jobs.edit', $job->id) }}" class="btn btn-info me-3">Edit</a>
            @endif
            <div class="dropdown mb-0">
                <button class="btn btn-tertiary dropdown-toggle" type="button" id="add-relationship" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-plus me-1"></i>Add
                </button>
                <ul class="dropdown-menu" aria-labelledby="add-relationship">
                    <li><a class="dropdown-item" href="{{ route('admin.locations.attach', ['jobs', $job->id]) }}">Location</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addTagForm">Tags</a></li>
                </ul>
            </div>
        </x-slot:buttons>
        <div>
            <x-slot:title>{{ $job->name }}</x-slot:title>
            <x-entities.show.field-summary>
                <a href="{{ $job->getOwnerShowLink($showRouteRootPrefix) }}">
                    {{ $job->owner->name }}
                </a>
            </x-entities.show.field-summary>
        </div>
        <x-entities.data.job :job="$job" showRouteRootPrefix="{{$showRouteRootPrefix}}"/>
    </x-entities.show>

    <x-tags.add :entity="$job" />

@endsection
