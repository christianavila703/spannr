@extends('layouts.panel')

@section('panel')
    <x-entities.show>
        <x-slot:breadcrumbs>
            <x-nav.entity-breadcrumbs :breadcrumbs="$fundraisingRound->generateBreadcrumbs('panel')"></x-nav.entity-breadcrumbs>
        </x-slot:breadcrumbs>
        <x-slot:title>{{ $fundraisingRound->name }}</x-slot:title>
        <x-entities.data.fundraising-round :fundraisingRound="$fundraisingRound" showRouteRootPrefix="{{$showRouteRootPrefix}}"/>
    </x-entities.show>
@endsection
