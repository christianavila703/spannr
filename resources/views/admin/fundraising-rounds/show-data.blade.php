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
            @if ($fundraisingRound->amount_raised == '')
            &ndash;
            @else
                ${{ number_format($fundraisingRound->amount_raised, 0) }}
            @endif
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-sack-dollar" tooltip="Valuation">
            @if ($fundraisingRound->valuation == '')
                &ndash;
            @else
                ${{ number_format($fundraisingRound->valuation, 0) }}
            @endif
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-typewriter" tooltip="Announcement">
            @if ($fundraisingRound->valuation == '')
                &ndash;
            @else
                <a href="{{ $fundraisingRound->announcement_url }}" target="_blank" rel="noopener noreferrer">Announcement<i class="fa fa-arrow-up-right-from-square ms-1"></i></a>
            @endif
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
