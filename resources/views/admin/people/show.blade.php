@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:breadcrumbs>{!! $person->breadcrumb !!}</x-slot:breadcrumbs>
        <x-slot:buttons>
            @if($person->isLocked && $person->lockedById !== auth()->user()->id)
                <x-entities.locked-label :record="$person" />
            @else
                <a href="{{ route('admin.people.edit', $person->id) }}" class="btn btn-info me-3">Edit</a>
            @endif

            @if ($person->ownedInvestor)
                <a href="{{ route('admin.investors.show', $person->ownedInvestor->slug) }}" class="btn btn-quintary me-3">
                    <i class="{{ $person->investor_icon }} me-1"></i>Investor
                </a>
            @else
                <x-entities.investors.create-from-entity entityType="{{ \App\Models\Person::class }}" entityId="{{ $person->id }}" />
            @endif

            <div class="dropdown mb-0">
                <button class="btn btn-tertiary dropdown-toggle" type="button" id="add-relationship" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-plus me-1"></i>Add
                </button>
                <ul class="dropdown-menu" aria-labelledby="add-relationship">
                    <li><a class="dropdown-item" href="{{ route('admin.locations.attach', ['people', $person->id]) }}">Location</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addTagForm">Tags</a></li>
                </ul>
            </div>
        </x-slot:buttons>
        <x-slot:title>{{ $person->name }}</x-slot:title>
        <x-entities.data.person-data :person="$person" />
    </x-entities.show>

    <x-tags.add :entity="$person" />

@endsection
