@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:title>{{ $organization->name }}</x-slot:title>
        <x-slot:buttons>
            <a href="{{ route('organizations.edit', $organization->id) }}" class="btn btn-info me-3">Edit</a>
            <div class="dropdown mb-0">
                <button class="btn btn-tertiary dropdown-toggle" type="button" id="add-relationship" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-plus me-1"></i>Add
                </button>
                <ul class="dropdown-menu" aria-labelledby="add-relationship">
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addInvestorForm">Investors</a></li>
                    <li><a class="dropdown-item" href="{{ route('organizations.people', $organization->slug) }}" >People</a></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addFundraisingRoundForm">Fundraising Round</a></li>
                    <li><a class="dropdown-item" href="{{ route('locations.attach', ['organizations', $organization->id]) }}">Location</a></li>
                </ul>
            </div>
        </x-slot:buttons>
        @include('admin.organizations.show-data')
    </x-entities.show>

    @include('admin.organizations.investors.add')
    @include('admin.organizations.people.add')
    @include('admin.organizations.fundraising-rounds.add')

@endsection
