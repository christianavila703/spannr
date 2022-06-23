<form method="POST" action="{{ $route }}" enctype="multipart/form-data">
    @csrf
    @isset($method)
        @method($method)
    @endisset
    <div class="row">
        <div class="col-12 col-md-6 col-lg-5">

            <x-forms.input type="text" name="name" label="Name" class="mb-3" value="{{ $event->name ?? old('name') }}" />

            <x-forms.input type="url" name="website" label="Website" class="mb-3" value="{{ $event->website ?? old('website') }}" />

            <div class="row">
                <div class="col-12 col-md-6">
                    <x-forms.input type="date" name="start_date" label="Start Date" value="{{ $event->start_date ?? old('start_date') }}" />
                </div>
                <div class="col-12 col-md-6">
                    <x-forms.input type="date" name="end_date" label="End Date" value="{{ $event->end_date ?? old('end_date') }}" />
                </div>
            </div>

            <div class="mb-3">
                <x-entities.show.field-heading title="Event Type(s)" />
                <div>
                    @foreach($eventTypes as $eventType)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="event_types[]" value="{{ $eventType->id }}" id="event-type-{{ $eventType->id }}"
                                @if (old('event_types'))
                                    @if(in_array($eventType->id, old('event_types'))) checked @endif
                                @else
                                    @isset($event)
                                        @if(in_array($eventType->id, $event->eventTypes->pluck('id')->toArray())) checked @endif
                                    @endisset
                                @endif
                            >
                            <label class="form-check-label" for="event-type-{{ $eventType->id }}">
                                {{ $eventType->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mb-3">
                <x-entities.show.field-heading title="Organizations" />
                <select multiple id="organizations" name="organizations[]" class="form-select">
                    @foreach ($organizations as $organization)
                        <option value="{{ $organization->id }}"
                            @if(old('organizations'))
                                @if(in_array($organization->id, old('organizations'))) selected @endif
                            @else
                                @isset($event)
                                    @if(in_array($organization->id, $event->organizations->pluck('id')->toArray())) selected @endif
                                @endisset
                            @endif
                        >{{ $organization->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <x-entities.show.field-heading title="People" />
                <select multiple id="people" name="people[]" class="form-select">
                    @foreach ($people as $person)
                        <option value="{{ $person->id }}"
                            @if(old('people'))
                                @if(in_array($person->id, old('people'))) selected @endif
                            @else
                                @isset($event)
                                    @if(in_array($person->id, $event->people->pluck('id')->toArray())) selected @endif
                                @endisset
                            @endif
                        >{{ $person->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <x-entities.show.field-heading title="Investors" />
                <select multiple id="investors" name="investors[]" class="form-select">
                    @foreach ($investors as $investor)
                        <option value="{{ $investor->id }}"
                            @if(old('investors'))
                                @if(in_array($investor->id, old('investors'))) selected @endif
                            @else
                                @isset($event)
                                    @if(in_array($investor->id, $event->investors->pluck('id')->toArray())) selected @endif
                                @endisset
                            @endif
                        >{{ $investor->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-5">
            <div class="form-floating mb-3">
                <textarea class="form-control floating-textarea-lg" id="description" name="description" placeholder="Description">{{ $event->description ?? old('description') }}</textarea>
                <label for="description">Description</label>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label"><x-entities.show.field-heading title="Image" /></label>
                <input class="form-control" type="file" name="image" id="image">
            </div>
        </div>
        <div class="col-12">
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" name="remote" id="remote"
                        @if(old('remote'))
                            checked
                        @else
                            @isset($event)
                                @if ($event->remote) checked @endif
                            @endisset
                        @endif
                    >
                    <label class="form-check-label fw-bold text-quaternary" for="remote">Remote / Virtual</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>

<script>
    const selectOrganizations = new Choices(document.getElementById('organizations'));
    const selectPeople = new Choices(document.getElementById('people'));
    const selectInvestors = new Choices(document.getElementById('investors'));
</script>
