<div class="row">
    <div class="col-12 col-md-6">
        <x-entities.show.field-summary>
            {{ $person->byline }}
        </x-entities.show.field-summary>

        <x-entities.show.field-group icon="fa-duotone fa-briefcase" tooltip="Job Type">
            {{ $person->job_type }}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-link" tooltip="Website">
            {!! $person->websiteLink !!}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-phone" tooltip="Phone">
            {!! auth()->user()->can('view contact info') ? $person->phone_link : '*****' !!}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-at" tooltip="Email">
            {!! auth()->user()->can('view contact info') ? $person->email_link : '*****' !!}
        </x-entities.show.field-group>

        <x-entities.show.field-heading title="Social Media" />

        <x-entities.show.field-group icon="fa-brands fa-facebook" tooltip="Facebook">
            {!! $person->facebookLink !!}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-brands fa-twitter" tooltip="Twitter">
            {!! $person->twitterLink !!}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-brands fa-linkedin" tooltip="LinkedIn">
            {!! $person->linkedinLink !!}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-brands fa-instagram" tooltip="Instagram">
            {!! $person->instagramLink !!}
        </x-entities.show.field-group>

        <x-entities.show.field-heading title="Organizations" />
        <x-entities.show.relationship-basic :records="$person->organizations" routeName="admin.organizations.show" />

        <x-entities.show.field-heading title="Investors" />
        <x-entities.show.relationship-basic :records="$person->investors" routeName="admin.investors.show" />

        <x-entities.show.field-heading title="Locations" />
        <x-entities.show.relationship-basic :records="$person->locations" routeName="admin.locations.show" />



        <x-entities.show.field-heading title="Tags" />
        <x-entities.show.relationship-tags :entity="$person"/>

        <x-entities.show.field-heading title="Bio" />
        <div>
            {!! nl2br(e($person->bio)) !!}
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="mb-2">
            @if($person->image != '')
                <img src="{{ $person->imageUrl }}" alt="{{ $person->name }}" class="entity-show-image">
            @endif
        </div>
    </div>
</div>
