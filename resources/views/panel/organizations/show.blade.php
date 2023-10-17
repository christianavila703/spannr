@extends('layouts.panel')

@section('panel')
    <x-entities.show>
        <x-slot:breadcrumbs>
            <x-nav.entity-breadcrumbs :breadcrumbs="$organization->generateBreadcrumbs('panel')"></x-nav.entity-breadcrumbs>
        </x-slot:breadcrumbs>
        <x-slot:title>{{ $organization->name }}</x-slot:title>
        <x-entities.data.organization :organization="$organization" showRouteRootPrefix="{{$showRouteRootPrefix}}"/>
    </x-entities.show>
@endsection
