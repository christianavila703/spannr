@extends('layouts.panel')

@section('panel')
    <x-entities.show>
        <x-slot:breadcrumbs>
            <x-nav.entity-breadcrumbs :breadcrumbs="$job->generateBreadcrumbs('panel')"></x-nav.entity-breadcrumbs>
        </x-slot:breadcrumbs>
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
@endsection
