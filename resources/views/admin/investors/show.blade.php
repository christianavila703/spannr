@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:breadcrumbs>{!! $investor->breadcrumb !!}</x-slot:breadcrumbs>
        <x-slot:buttons>
            @if($investor->isLocked && $investor->lockedById !== auth()->user()->id)
                <x-entities.locked-label :record="$investor" />
            @else
                <a href="{{ route('admin.investors.edit', $investor->id) }}" class="btn btn-info me-3">Edit</a>
            @endif
            @if($investor->ownable_id)
                <a href="{{ $investor->ownable_admin_link }}" class="btn btn-quintary me-3">
                    <i class="{{ $investor->ownable_icon }}"></i>
                    {{ $investor->ownable->name }}
                </a>
            @endif

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

        @if($investor->ownable_id)
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="investor-tab" data-bs-toggle="tab" data-bs-target="#investor-tab-pane"
                            type="button" role="tab" aria-controls="investor-tab-pane" aria-selected="true">Investor Details
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="entity-tab" data-bs-toggle="tab" data-bs-target="#entity-tab-pane"
                            type="button" role="tab" aria-controls="entity-tab-pane" aria-selected="false">
                        {{ $investor->ownable_pretty_type }} Details
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade p-2 show active" id="investor-tab-pane" role="tabpanel" aria-labelledby="investor-tab" tabindex="0">
                    @include('admin.investors.show-data')
                </div>
                <div class="tab-pane fade p-2" id="entity-tab-pane" role="tabpanel" aria-labelledby="entity-tab" tabindex="0">
                    @if($investor->ownable_type === \App\Models\Organization::class)
                        <x-entities.data.organization-data :organization="$investor->ownable" />
                    @endif

                    @if($investor->ownable_type === \App\Models\Person::class)
                        <x-entities.data.person-data :person="$investor->ownable" />
                    @endif
                </div>
            </div>
        @else
            @include('admin.investors.show-data')
        @endif

    </x-entities.show>

    <x-tags.add :entity="$investor" />

@endsection
