<div class="row">
    <div class="col-12 col-md-6">

        <x-entities.show.relationship-list :records="$event->eventTypes" />

        <x-entities.show.field-group icon="fa-duotone fa-calendar-days" tooltip="Date">
            @include('admin.events.fields.dates')
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-link" tooltip="Website">
            {!! $event->websiteLink !!}
        </x-entities.show.field-group>

        <x-entities.show.field-heading title="Organizations" />
        <x-entities.show.relationship-basic :records="$event->organizations" routeName="organizations.show" />

        <x-entities.show.field-heading title="Investors" />
        <x-entities.show.relationship-basic :records="$event->investors" routeName="investors.show" />

        <x-entities.show.field-heading title="People" />
        <x-entities.show.relationship-basic :records="$event->people" routeName="people.show" />

        <x-entities.show.field-heading title="Locations" />
        @if($event->remote)
            <p class="my-2 text-quaternary fw-bold">Remote / Virtual</p>
        @endif
        <x-entities.show.relationship-basic :records="$event->locations" routeName="locations.show" />
    </div>
    <div class="col-12 col-md-6">
        <div class="mb-2">
            @if($event->image != '')
                <img src="{{ $event->imageUrl }}" alt="{{ $event->name }}" class="entity-show-image">
            @endif

            <x-entities.show.field-heading title="Description" />
            <div>
                {!! nl2br(e($event->description)) !!}
            </div>
        </div>
    </div>
</div>
