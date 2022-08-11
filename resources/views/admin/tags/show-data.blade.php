<div class="row">
    <div class="col-12 col-md-6">
        <x-entities.show.field-heading title="Organizations" />
        <x-entities.show.relationship-basic :records="$tag->organizations" routeName="admin.organizations.show" />

        <x-entities.show.field-heading title="Investors" />
        <x-entities.show.relationship-basic :records="$tag->investors" routeName="admin.investors.show" />

        <x-entities.show.field-heading title="People" />
        <x-entities.show.relationship-basic :records="$tag->people" routeName="admin.people.show" />

        <x-entities.show.field-heading title="Events" />
        <x-entities.show.relationship-basic :records="$tag->events" routeName="admin.events.show" />

        <x-entities.show.field-heading title="Jobs" />
        <x-entities.show.relationship-basic :records="$tag->jobs" routeName="admin.jobs.show" />
    </div>
    <div class="col-12 col-md-6">
        <div>
            <x-entities.show.field-heading title="Description" />
            {!! $tag->description ?? '&ndash;' !!}
        </div>
        <div>
            @if ($tag->children->count() > 0)
                <x-entities.show.field-heading title="Child Tags" />
            @endif
            @foreach ($tag->children as $child)
                <div>
                    <div class="py-1">
                        <a href="{{ route('admin.tags.show', $child->slug) }}">{{ $child->name }}</a>
                    </div>

                    @foreach ($child->children as $grandchild)
                        <div class="ms-4 py-1">
                            <a href="{{ route('admin.tags.show', $grandchild->slug) }}">{{ $grandchild->name }}</a>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>
