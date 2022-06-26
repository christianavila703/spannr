<div class="row">
    <div class="col-12 col-md-6">
        <x-entities.show.field-summary>
            {{ $organization->summary }}
        </x-entities.show.field-summary>

        <x-entities.show.field-heading title="Details" />

        <x-entities.show.field-group icon="fa-duotone fa-buildings" tooltip="Organization type">
            {{ $organization->type }}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-calendar-days" tooltip="Founded">
            {{ $organization->founded_date }}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-link" tooltip="Website">
            {!! $organization->websiteLink !!}
        </x-entities.show.field-group>

        <x-entities.show.field-heading title="Social Media" />

        <x-entities.show.field-group icon="fa-brands fa-facebook" tooltip="Facebook">
            {!! $organization->facebookLink !!}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-brands fa-twitter" tooltip="Twitter">
            {!! $organization->twitterLink !!}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-brands fa-linkedin" tooltip="LinkedIn">
            {!! $organization->linkedinLink !!}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-brands fa-instagram" tooltip="Instagram">
            {!! $organization->instagramLink !!}
        </x-entities.show.field-group>

        <x-entities.show.field-heading title="Investors" />
        <x-entities.show.relationship-basic :records="$organization->investors" routeName="investors.show" />

        <x-entities.show.field-heading title="People" />
        <x-entities.show.relationship-with-pivot :records="$organization->people" routeName="people.show" pivotName="role" />

        <x-entities.show.field-heading title="Fundraising Rounds" />
        <x-entities.show.relationship-fundraising-round :records="$organization->fundraisingRounds" />

        <x-entities.show.field-heading title="Locations" />
        <x-entities.show.relationship-basic :records="$organization->locations" routeName="locations.show" />
    </div>
    <div class="col-12 col-md-6">
        <div class="mb-2">
            @if($organization->image != '')
                <img src="{{ $organization->imageUrl }}" alt="{{ $organization->name }}" class="entity-show-image">
            @endif
            <x-entities.show.field-heading title="Description" />
            <div>
                {!! nl2br(e($organization->description)) !!}
            </div>
        </div>
    </div>
</div>
