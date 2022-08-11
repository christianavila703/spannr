@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:breadcrumbs>{!! $investor->breadcrumb !!}</x-slot:breadcrumbs>
        <x-slot:buttons>
            <a href="{{ route('admin.investors.edit', $investor->id) }}" class="btn btn-info me-3">Edit</a>

            <div class="dropdown mb-0">
                <button class="btn btn-tertiary dropdown-toggle" type="button" id="add-relationship" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-plus me-1"></i>Add
                </button>
                <ul class="dropdown-menu" aria-labelledby="add-relationship">
                    <li><a class="dropdown-item" href="{{ route('admin.locations.attach', ['investors', $investor->id]) }}">Location</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.investors.people', $investor->slug) }}">People</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addTagForm">Tags</a></li>
                </ul>
            </div>
        </x-slot:buttons>
        <x-slot:title>{{ $investor->name }}</x-slot:title>
        @include('admin.investors.show-data')
    </x-entities.show>

    <x-tags.add route="{{ route('admin.tags.sync') }}" entityType="investors" entityName="{{ $investor->name }}" entityId="{{ $investor->id }}" />

@endsection
