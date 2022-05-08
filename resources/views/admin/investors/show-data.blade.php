<div class="row">
    <div class="col-12 col-md-6">
        <x-entities.show.field-summary>
            {{ $investor->summary }}
        </x-entities.show.field-summary>

        <x-entities.show.field-heading title="Details" />
        <x-entities.show.field-group icon="fa-duotone fa-buildings" tooltip="Investor type">
            {{ $investor->type }}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-money-check-dollar-pen" tooltip="Investor type">
            {totalRaised}
        </x-entities.show.field-group>

        <x-entities.show.field-group icon="fa-duotone fa-earth-americas" tooltip="Website">
            {!! $investor->websiteLink !!}
        </x-entities.show.field-group>

        <x-entities.show.field-heading title="Social Media" />

        <x-entities.show.field-group icon="fa-brands fa-facebook" tooltip="Facebook">
            {!! $investor->facebookLink !!}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-brands fa-twitter" tooltip="Twitter">
            {!! $investor->twitterLink !!}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-brands fa-linkedin" tooltip="LinkedIn">
            {!! $investor->linkedinLink !!}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-brands fa-instagram" tooltip="Instagram">
            {!! $investor->instagramLink !!}
        </x-entities.show.field-group>

        <x-entities.show.field-heading title="Organizations" />
        <x-entities.show.relationship-basic :records="$investor->organizations" routeName="organizations.show" />

        <x-entities.show.field-heading title="Fundraising Rounds" />
        <x-entities.show.relationship-fundraising-round :records="$investor->fundraisingRounds" />

        <x-entities.show.field-heading title="Description" />
        <div>
            {!! nl2br(e($investor->description)) !!}
        </div>
    </div>
</div>
