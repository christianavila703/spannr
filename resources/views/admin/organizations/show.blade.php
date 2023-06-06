@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:breadcrumbs>{!! $organization->breadcrumb !!}</x-slot:breadcrumbs>
        <x-slot:buttons>
            @if($organization->isLocked && $organization->lockedById !== auth()->user()->id)
                <x-entities.locked-label :record="$organization" />
            @else
                <a href="{{ route('admin.organizations.edit', $organization->id) }}" class="btn btn-info me-3">Edit</a>
            @endif
            @if ($organization->ownedInvestor)
                <a href="{{ route('admin.investors.show', $organization->ownedInvestor->slug) }}" class="btn btn-quintary me-3">
                    <i class="{{ $organization->investor_icon }} me-1"></i>Investor
                </a>
            @else
                <x-entities.investors.create-from-entity entityType="{{ \App\Models\Organization::class }}" entityId="{{ $organization->id }}" />
            @endif

            @if($organization->stock)
                <a href="{{ route('public.content.organizations.show', $organization->slug) }}" class="btn btn-quaternary me-3">View Public</a>
            @endif
            <div class="dropdown mb-0">
                <button class="btn btn-tertiary dropdown-toggle" type="button" id="add-relationship" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-plus me-1"></i>Add
                </button>
                <ul class="dropdown-menu" aria-labelledby="add-relationship">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addInvestorForm">Investors</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.organizations.people', $organization->slug) }}" >People</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addFundraisingRoundForm">Fundraising Round</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.locations.attach', ['organizations', $organization->id]) }}">Location</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addTagForm">Tags</a></li>
                    @if($organization->type == \App\Models\Organization::TYPE_PUBLIC)
                        @if($organization->stock)
                            <li><a class="dropdown-item" href="{{ route('admin.organizations.stock.edit', $organization->stock->id) }}">Edit Stock</a></li>
                        @else
                            <li><a class="dropdown-item" href="{{ route('admin.organizations.stock.add', $organization->slug) }}">Stock</a></li>
                        @endif
                    @endif
                </ul>
            </div>

            @include('admin.entity-reviews.includes.entity-form', ['entity' => $organization])
        </x-slot:buttons>
        <x-slot:title>{{ $organization->name }}</x-slot:title>
        <x-entities.data.organization-data :organization="$organization" />
    </x-entities.show>

    @include('admin.organizations.investors.add')
    @include('admin.organizations.people.add')
    @include('admin.organizations.fundraising-rounds.add')

    <x-tags.add :entity="$organization" />

@endsection
