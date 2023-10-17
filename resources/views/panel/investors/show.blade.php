@extends('layouts.panel')

@section('panel')
    <x-entities.show>
        <x-slot:breadcrumbs>
            <x-nav.entity-breadcrumbs :breadcrumbs="$investor->generateBreadcrumbs('panel')"></x-nav.entity-breadcrumbs>
        </x-slot:breadcrumbs>

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
                    <x-entities.data.investor :investor="$investor" showRouteRootPrefix="{{$showRouteRootPrefix}}"/>
                </div>
                <div class="tab-pane fade p-2" id="entity-tab-pane" role="tabpanel" aria-labelledby="entity-tab" tabindex="0">
                    @if($investor->ownable_type === \App\Models\Organization::class)
                        <x-entities.data.organization :organization="$investor->ownable" showRouteRootPrefix="{{$showRouteRootPrefix}}"/>
                    @endif

                    @if($investor->ownable_type === \App\Models\Person::class)
                        <x-entities.data.person :person="$investor->ownable" showRouteRootPrefix="{{$showRouteRootPrefix}}"/>
                    @endif
                </div>
            </div>
        @else
            <x-entities.data.investor :investor="$investor" showRouteRootPrefix="{{$showRouteRootPrefix}}"/>
        @endif
    </x-entities.show>
@endsection
