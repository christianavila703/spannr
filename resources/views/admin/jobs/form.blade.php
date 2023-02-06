@push('head')
    @trixassets
@endpush

<form method="POST" action="{{ $route }}" enctype="multipart/form-data">
    @csrf
    @isset($method)
        @method($method)
    @endisset
    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="mb-3">
                @isset($job)
                    {!! $job->trix('description') !!}

                @else
                    @trix(\App\Models\Job::class, 'description')
                @endisset
            </div>
        </div>
        <div class="col-12 col-lg-6">

            <x-forms.input type="text" name="name" label="Job Title" class="mb-3" value="{{ $job->name ?? old('name') }}" />

            <x-entities.show.field-heading title="Job Owner" />

            <div class="row">
                <div class="col-12 col-lg-6 form-input-min-height mb-2">
                    @if(old('owner_id'))
                        {{ old('owner_id') }}
                    @endif

                    @if(old('owner_type'))
                        {{ old('owner_type') }}
                    @endif

                    @foreach ($ownerTypes as $label => $value)
                        <div class="form-check">
                            <input class="form-check-input owner-type-radio" type="radio" name="owner_type" id="owner_type-{{ $label }}"
                                   value="{{ $value }}"
                                   data-container="{{ $label }}-list"
                                   data-element="owner_id_{{ $label }}"
                               @if(old('owner_type'))
                                    @if(old('owner_type') == $value) checked @endif
                               @else
                                   @isset($job)
                                        @if($job->owner_type == $value) checked @endif
                                   @else
                                       @if ($ownerTypes->first() == $value) checked @endif
                                   @endisset
                               @endif
                            >
                            <label class="form-check-label" for="owner_type-{{ $label }}">
                                {{ ucwords($label) }}
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="col-12 col-lg-6">
                    @foreach ($ownerTypes as $typeLabel => $typeValue)
                        <div id="{{ $typeLabel }}-list" class="mb-3"
                             @if(old('owner_type'))
                                 @if(old('owner_type') == $typeValue)
                                    style="display: block"
                                 @else
                                    style="display: none"
                                 @endif
                             @else
                                 @isset($job)
                                    @if($job->owner_type == $typeValue)
                                        style="display: block"
                                    @else
                                        style="display: none"
                                    @endif
                                 @else
                                    @if ($ownerTypes->first() == $typeValue)
                                        style="display: block"
                                    @else
                                        style="display: none"
                                    @endif
                                 @endisset
                             @endif
                        >
                            <label for="owner_id_{{ $typeLabel }}" class="visually-hidden">Owner</label>
                            <select id="owner_id_{{ $typeLabel }}" class="form-select">
                                <option value="" selected>Select {{ ucwords($typeLabel) }}</option>
                                @foreach ($ownerLists[$typeLabel] as $jobOwner)
                                    <option value="{{ $jobOwner->id }}"
                                        @if(old('owner_id'))
                                            @if (old('owner_type') == $typeValue)
                                                @if(old('owner_id') == $jobOwner->id) selected @endif
                                            @endif
                                        @else
                                            @isset($job)
                                                @if($job->owner_type == $typeValue AND $job->owner_id == $jobOwner->id) selected @endif
                                            @endisset
                                        @endif
                                    >
                                        {{ $jobOwner->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                    <input type="hidden" name="owner_id" id="owner_id"
                        @if(old('owner_id'))
                            value="{{ old('owner_id') }}"
                        @else
                            @isset($job)
                                value="{{ $job->owner_id }}"
                            @endisset
                        @endif
                    >
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-6">
                    <x-forms.select name="type" label="Job Type">
                        @foreach (\App\Models\Job::TYPES as $type)
                            <option value="{{ $type }}"
                                @isset($job)
                                    @if($job->type == $type) selected @endif
                                @else
                                    @if(old('type') == $type) selected @endif
                                @endisset
                            >{{ $type }}</option>
                        @endforeach
                    </x-forms.select>
                </div>
                <div class="col-12 col-lg-6">
                    <x-forms.select name="status" label="Status">
                        @foreach (\App\Models\Job::STATUSES as $status)
                            <option value="{{ $status }}"
                                @isset($job)
                                    @if($job->status == $status) selected @endif
                                @else
                                    @if(old('status') == $status) selected @endif
                                @endisset
                            >{{ $status }}</option>
                        @endforeach
                    </x-forms.select>
                </div>
            </div>

            <div class="mb-3">
                <label for="category_id" class="visually-hidden">Category</label>
                <select id="category_id" name="category_id" class="form-select">
                    <option value="" selected>Select Job Category</option>
                    @foreach ($jobCategories as $jobCategory)
                        <option value="{{ $jobCategory->id }}"
                            @isset($job)
                                @if($job->category_id == $jobCategory->id) selected @endif
                            @else
                                @if(old('category_id') == $jobCategory->id) selected @endif
                            @endisset
                        >{{ $jobCategory->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-12 col-lg-6">
                    <x-forms.input type="number" name="salary_min" label="Salary min" class="mb-3" value="{{ $job->salary_min ?? old('salary_min') }}" required/>
                    <x-forms.input type="number" name="salary_max" label="Salary max" class="mb-3" value="{{ $job->salary_max ?? old('salary_max') }}" />
                </div>
                <div class="col-12 col-lg-6">
                    <x-forms.input type="number" name="hourly" label="Hourly" class="mb-3" value="{{ $job->hourly ?? old('hourly') }}" />
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-6">
                    <x-forms.input type="date" name="posted_date" label="Posted Date" value="{{ $job->posted_date ?? old('posted_date') }}" />
                    <button id="setPostedDate" class="btn btn-sm p-1 mb-3 mt-1 text-muted" data-date="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">Set to today</button>
                </div>
                <div class="col-12 col-lg-6">
                    <x-forms.input type="date" name="exp_date" label="Expiration Date" class="mb-3" value="{{ $job->exp_date ?? old('exp_date') }}" />
                </div>
            </div>

            <x-forms.input type="url" name="url" label="Application URL" class="mb-3" value="{{ $job->url ?? old('url') }}" />

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" name="remote" id="remote"
                        @if(old('remote'))
                            checked
                        @else
                            @isset($job)
                                @if ($job->remote) checked @endif
                            @endisset
                        @endif
                    >
                    <label class="form-check-label fw-bold text-quaternary" for="remote">Remote / Virtual</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
    <script>
        const jobOwnerOrganization = new Choices(document.getElementById('owner_id_organization'));
        const jobOwnerInvestor = new Choices(document.getElementById('owner_id_investor'));
        const jobCategory = new Choices(document.getElementById('category_id'));
        const ownerIdElement = document.getElementById('owner_id');
        const ownerTypes = document.querySelectorAll('.owner-type-radio');
        const postedDateButton = document.getElementById('setPostedDate');
        const postedDateElement = document.getElementById('posted_date');

        jobOwnerOrganization.passedElement.element.addEventListener('addItem', function(event) {
            setOwnerIdValue(event.detail.value);
        });

        jobOwnerInvestor.passedElement.element.addEventListener('addItem', function(event) {
            setOwnerIdValue(event.detail.value);
        });

        ownerTypes.forEach((ownerTypeRadio) => {
           ownerTypeRadio.addEventListener('click', function(event) {
               setOwnerType();
           });
        });

        postedDateButton.addEventListener('click', function(event) {
           event.preventDefault();
           postedDateElement.value = event.currentTarget.dataset.date;
        });

        function setOwnerType() {
            let radios = document.querySelectorAll('.owner-type-radio');

            radios.forEach((radio) => {
                let ownerTypeContainer = document.getElementById(radio.dataset.container);
                let ownerSelectElement = document.getElementById(radio.dataset.element).value;

                if (radio.checked === true) {
                    ownerTypeContainer.style.display = "block";
                    setOwnerIdValue(ownerSelectElement);
                } else {
                    ownerTypeContainer.style.display = "none";
                }
            });
        }

        function setOwnerIdValue(value) {
            ownerIdElement.value = value;
        }
    </script>
</form>
