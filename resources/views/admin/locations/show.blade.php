@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:breadcrumbs>{!! $location->breadcrumb !!}</x-slot:breadcrumbs>
        <x-slot:buttons>
            @if($location->isLocked && $location->lockedById !== auth()->user()->id)
                <x-entities.locked-label :record="$location" />
            @else
                <a href="{{ route('admin.locations.edit', $location->id) }}" class="btn btn-info me-3">Edit</a>
            @endif

{{--            <div class="dropdown mb-0">--}}
{{--                <button class="btn btn-tertiary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">--}}
{{--                    <i class="fa-solid fa-plus me-1"></i>Add--}}
{{--                </button>--}}
{{--                <ul class="dropdown-menu" aria-labelledby="add-relationship">--}}
{{--                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addInvestorForm">Investors</a></li>--}}
{{--                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addPeopleForm">People</a></li>--}}
{{--                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addOrganizationForm">Organizations</a></li>--}}
{{--                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addJobsForm">Jobs</a></li>--}}
{{--                </ul>--}}
{{--            </div>--}}

        </x-slot:buttons>
        <x-slot:title>{{ $location->name }}</x-slot:title>

        <p class="lead mb-2">
            Latitude: {{$location->latitude}}
        </p>
        <p class="lead mb-2">
            Longitude: {{$location->longitude}}
        </p>

        <div class="row">
            <div class="col-12 col-md-6">
                <x-entities.show.field-heading title="Investors" />
                <x-entities.show.relationship-basic :records="$location->investors" routeName="admin.investors.show" />

                <x-entities.show.field-heading title="People" />
                <x-entities.show.relationship-basic :records="$location->people" routeName="admin.people.show" />

                <x-entities.show.field-heading title="Organizations" />
                <x-entities.show.relationship-basic :records="$location->organizations" routeName="admin.organizations.show" />

                <x-entities.show.field-heading title="Jobs" />
                <x-entities.show.relationship-basic :records="$location->jobs" routeName="admin.jobs.show" />

                <x-entities.show.field-heading title="Job Applications" />
                <x-entities.show.relationship-list :records="$location->jobApplications" />
            </div>
        </div>
    </x-entities.show>

@endsection
