@extends('layouts.app')

@section('content')

    <x-entities.show>
        <div>
            <x-slot:title>{{ $job->name }}</x-slot:title>
            <x-entities.show.field-summary>
                <a href="{{ $job->owner_show_link }}">
                    {{ $job->owner->name }}
                </a>
            </x-entities.show.field-summary>
        </div>
        <x-slot:buttons>
            <a href="{{ route('admin.jobs.edit', $job->id) }}" class="btn btn-info me-3">Edit</a>
            <div class="dropdown mb-0">
                <button class="btn btn-tertiary dropdown-toggle" type="button" id="add-relationship" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-plus me-1"></i>Add
                </button>
                <ul class="dropdown-menu" aria-labelledby="add-relationship">
                    <li><a class="dropdown-item" href="{{ route('admin.locations.attach', ['jobs', $job->id]) }}">Location</a></li>
                </ul>
            </div>
        </x-slot:buttons>
        @include('admin.jobs.show-data')
    </x-entities.show>

@endsection
