@extends('layouts.panel')

@section('panel')
    <x-entities.show>
        <x-slot:breadcrumbs>
            <x-nav.entity-breadcrumbs :breadcrumbs="$location->generateBreadcrumbs('panel')"></x-nav.entity-breadcrumbs>
        </x-slot:breadcrumbs>
        <x-slot:title>{{ $location->name }}</x-slot:title>
        <x-entities.data.location :location="$location" showRouteRootPrefix="{{$showRouteRootPrefix}}"/>
    </x-entities.show>
@endsection
