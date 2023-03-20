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
        @include('admin.people.show-data')
    </x-entities.show>

    <x-tags.add route="{{ route('admin.tags.sync') }}" entityType="people" entityName="{{ $person->name }}" entityId="{{ $person->id }}" />

@endsection
