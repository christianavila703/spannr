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

        <x-entities.show.field-heading title="Articles" />
        <x-entities.show.relationship-basic :records="$tag->articles" routeName="admin.articles.show" />

        <x-entities.show.field-heading title="Content" />
        <x-entities.show.relationship-basic :records="$tag->content" routeName="admin.jobs.content" />

        <x-entities.show.field-heading title="Listings" />
        <x-entities.show.relationship-listings :records="$tag->listings" />
    </div>
    <div class="col-12 col-md-6">
        @if(!$tag->parent_id)
        <div>
            <x-entities.show.field-heading title="Available for entities" />
            @if ($tag->entity_types)
                @foreach($tag->entity_types as $entityType)
                    {{ ucfirst(array_flip(App\Models\Tag::ENTITY_TYPES)[$entityType]) }}@if(!$loop->last),&nbsp;@endif
                @endforeach
            @else
                –
            @endif
        </div>
        @endif
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
