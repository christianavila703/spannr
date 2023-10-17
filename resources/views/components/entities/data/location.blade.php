<p class="lead mb-2">
    Latitude: {{$location->latitude}}
</p>
<p class="lead mb-2">
    Longitude: {{$location->longitude}}
</p>

<div class="row">
    <div class="col-12 col-md-6">
        <x-entities.show.field-heading title="Investors" />
        <x-entities.show.relationship-basic :records="$location->investors" routeName="{{$showRouteRootPrefix}}.investors.show" />

        <x-entities.show.field-heading title="People" />
        <x-entities.show.relationship-basic :records="$location->people" routeName="{{$showRouteRootPrefix}}.people.show" />

        <x-entities.show.field-heading title="Organizations" />
        <x-entities.show.relationship-basic :records="$location->organizations" routeName="{{$showRouteRootPrefix}}.organizations.show" />

        <x-entities.show.field-heading title="Jobs" />
        <x-entities.show.relationship-basic :records="$location->jobs" routeName="{{$showRouteRootPrefix}}.jobs.show" />

        <x-entities.show.field-heading title="Events" />
        <x-entities.show.relationship-basic :records="$location->events" routeName="{{$showRouteRootPrefix}}.event.show" />
    </div>
</div>
