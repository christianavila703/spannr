@extends('layouts.panel')

@section('panel')
    <x-entities.show>
        <x-slot:breadcrumbs>
            <x-nav.entity-breadcrumbs :breadcrumbs="$event->generateBreadcrumbs('panel')"></x-nav.entity-breadcrumbs>
        </x-slot:breadcrumbs>
        <x-slot:title>{{ $event->name }}</x-slot:title>
        <x-entities.data.event :event="$event" showRouteRootPrefix="{{$showRouteRootPrefix}}"/>
    </x-entities.show>
@endsection
