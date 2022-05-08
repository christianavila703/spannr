<div class="row">
    <div class="col-12 col-md-6">
        <x-entities.show.field-heading title="Details" />
        <x-entities.show.field-group icon="fa-duotone fa-coins" tooltip="Round">
            {{ $fundraisingRound->round }}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-calendar-days" tooltip="Date">
            {{ $fundraisingRound->date }}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-money-check-dollar-pen" tooltip="Amount Raised">
            ${{ number_format($fundraisingRound->amount_raised, 0) }}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-sack-dollar" tooltip="Valuation">
            ${{ number_format($fundraisingRound->valuation, 0) }}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-typewriter" tooltip="Announcement">
            <a href="{{ $fundraisingRound->announcement_url }}" target="_blank" rel="noopener noreferrer">Announcement<i class="fa fa-arrow-up-right-from-square ms-1"></i></a>
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-building" tooltip="Organization">
            <a href="{{ route('organizations.show', $fundraisingRound->organization->slug) }}">{{ $fundraisingRound->organization->name }}</a>
        </x-entities.show.field-group>

        <x-entities.show.field-heading title="Investors" />
        <x-entities.show.relationship-basic :records="$fundraisingRound->investors" routeName="investors.show" />

        <x-entities.show.field-heading title="Description" />
        <div>
            {!! nl2br(e($fundraisingRound->description)) !!}
        </div>
    </div>
</div>
