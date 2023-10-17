@extends('layouts.app')

@section('content')

    <x-entities.show>
        <x-slot:breadcrumbs>
            <x-nav.entity-breadcrumbs :breadcrumbs="$location->generateBreadcrumbs()"></x-nav.entity-breadcrumbs>
        </x-slot:breadcrumbs>
        <x-slot:buttons>
            @if($location->isLocked && $location->lockedById !== auth()->user()->id)
                <x-entities.locked-label :record="$location" />
            @else
                <a href="{{ route('admin.locations.edit', $location->slug) }}" class="btn btn-info me-3">Edit</a>
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

        <x-entities.data.location :location="$location"  showRouteRootPrefix="{{$showRouteRootPrefix}}"/>
    </x-entities.show>

@endsection
