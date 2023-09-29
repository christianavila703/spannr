<form method="POST" action="{{ $route }}" enctype="multipart/form-data">
    @csrf
    @isset($method)
        @method($method)
    @endisset
    <div class="row">
        <div class="col-12 col-md-6 col-lg-5">

            <x-forms.input type="text" name="name" label="Name" class="mb-3" value="{{ $organization->name ?? old('name') }}" />

            <div class="form-floating mb-3">
                <select class="form-select" id="longevity" name="longevity" aria-label="Select longevity">
                    <option selected disabled>Select Longevity</option>
                    <option value="1" {{ isset($organization) && $organization->longevity === 1 ? 'selected' : null }}>Yes</option>
                    <option value="0" {{ isset($organization) && $organization->longevity === 0 ? 'selected' : null }}>No</option>
                </select>
                <label for="longevity">Longevity related</label>
            </div>

            <div id="organization_tags_block" @if(!isset($organization) || $organization->longevity !== 1) style="display: none;" @endif>
                <div class="form-floating mb-3">
                    <select class="form-select" id="parent_tag" name="parent_tag" aria-label="Parent Tag">
                        <option selected disabled>Select Parent Tag</option>
                        @foreach($parentTags as $id => $name)
                            <option value="{{ $id }}"
                                @if(isset($currentTags) && $currentTags && in_array($id, $currentTags)) selected @endif >
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    <label for="longevity">Parent Tag</label>
                </div>

                <div id="organization_child_tags_block" class="mb-3" @if(!isset($childTags) || empty($childTags)) style="display: none;" @endif>
                    <label for="child_tags" class="visually-hidden">Child Tags</label>
                    <select class="form-select" id="child_tags" name="child_tags[]" aria-label="Child Tags" multiple data-url="{{ route('admin.json.childTagsByParentId') }}">
                        <option value="" disabled>Child Tags</option>
                        @if(isset($childTags))
                            @foreach($childTags as $id => $name)
                                <option value="{{ $id }}"
                                        @if(isset($currentTags) && $currentTags && in_array($id, $currentTags)) selected @endif >
                                    {{ $name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

        </div>

        <div class="col-12 col-md-6 col-lg-5">
            <div class="form-floating mb-3">
                <select class="form-select" id="type" name="type" aria-label="Select type">
                    <option selected disabled>Select Type</option>
                    @foreach (\App\Models\Organization::TYPES as $type)
                        <option value="{{ $type }}"
                                @isset($organization)
                                    @if($organization->type == $type) selected @endif
                                @else
                                    @if(old('type') == $type) selected @endif
                            @endisset
                        >{{ $type }}</option>
                    @endforeach
                </select>
                <label for="type">Type of organization</label>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 col-md-6 col-lg-5">

            <x-forms.input type="url" name="website" label="Website" class="mb-3" value="{{ $organization->website ?? old('website') }}" />

            <x-forms.input type="number" name="founded_date" label="Year Founded" class="mb-3" value="{{ $organization->founded_date ?? old('founded_date') }}" />

            <div class="form-floating mb-3">
                <textarea class="form-control floating-textarea-sm" id="summary" name="summary" placeholder="Summary">{{ $organization->summary ?? old('summary') }}</textarea>
                <label for="summary">Tagline / Summary</label>
            </div>
            <div class="mb-3">
                <p class="h4 text-tertiary mb-0">Social Media</p>
                <p class="text-muted small mb-1">
                    Handles only -- do not include the full URL
                </p>
                <x-forms.input-group type="text" name="facebook" label="https://www.facebook.com/" class="mb-1" value="{{ $organization->facebook ?? old('facebook') }}" />
                <x-forms.input-group type="text" name="twitter" label="https://www.twitter.com/" class="mb-1" value="{{ $organization->twitter ?? old('twitter') }}" />
                <x-forms.input-group type="text" name="linkedin" label="https://www.linkedin.com/" class="mb-1" value="{{ $organization->linkedin ?? old('linkedin') }}" />
                <x-forms.input-group type="text" name="instagram" label="https://www.instagram.com/" class="mb-1" value="{{ $organization->instagram ?? old('instagram') }}" />

            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-5">
            <div class="form-floating mb-3">
                <textarea class="form-control floating-textarea-xl" id="description" name="description" placeholder="Description">{!! $organization->description ?? old('description') !!}</textarea>
                <label for="description">Description / Content Area</label>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label"><x-entities.show.field-heading title="Logo" /></label>
                <input class="form-control" type="file" name="image" id="image">
            </div>

            @if(isset($organization) && $organization->stock !== null)
                <div class="mb-3">
                    <p class="h5 text-uppercase mb-0">Stock and ticker:</p>
                    <span class="badge bg-primary">{{ $organization->stock->ticker }}</span>
                    @if($organization->stock->indexed === 0)
                    <label for="remove_stock">
                        <input type="checkbox" id="remove_stock" name="remove_stock" value="1" /> Remove assigned ticker?
                        <small class="text-muted">(only if the stock doesn't participate in the index)</small>
                    </label>
                    @endif
                </div>
            @endif
        </div>
        <div class="col-12 mb-3">
            <x-tiny-mce.tiny-config />
            <x-entities.show.field-heading title="Extended Description" />
            <x-tiny-mce.tiny-editor id="extended_description" name="extended_description" value="{{ $organization->extended_description ?? old('extended_description') }}"/>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>

@push('end')
    @vite('resources/js/organization-form-tags.js')
@endpush
