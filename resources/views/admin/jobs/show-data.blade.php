<div class="row d-flex">
    <div class="col-12 col-lg-7 order-2 order-lg-1">
        <div class="mb-2">
            <x-entities.show.field-heading title="Job Description" />
            <div>
                {!! $job->trixRender('description') !!}
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-5 order-1 order-lg-2 ps-lg-4">
        <x-entities.show.field-heading title="Details" />

        <x-entities.show.field-group icon="fa-duotone fa-clock" tooltip="Status">
            {{ $job->status }}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-briefcase" tooltip="Job type">
            {{ $job->type }}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-user-doctor" tooltip="Job category">
            {{ $job->category->name }}
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-calendar-days" tooltip="Posted Date">
            {{ $job->pretty_posted_date }}

            @if ($job->exp_date)
                <span class="ms-1 text-muted small">(archived {{ $job->pretty_exp_date }})</span>
            @endif
        </x-entities.show.field-group>
        <x-entities.show.field-group icon="fa-duotone fa-money-check-dollar" tooltip="Salary">
            @if($job->salary)
                ${{ number_format($job->salary, 0) }}
            @endif
            @if($job->hourly)
                ${{ number_format($job->hourly, 0) }} / hour
            @endif
        </x-entities.show.field-group>
        <x-entities.show.field-heading title="Locations" />
        @if($job->remote)
            <p class="my-2 text-quaternary fw-bold">Remote / Virtual</p>
        @endif
        <x-entities.show.relationship-basic :records="$job->locations" routeName="admin.locations.show" />
        <x-entities.show.field-heading title="Application URL" />
        <x-entities.show.field-group icon="fa-duotone fa-link" tooltip="Application URL">
            {!! $job->urlLink !!}
        </x-entities.show.field-group>
    </div>
</div>
