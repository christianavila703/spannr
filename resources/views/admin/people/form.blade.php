<form method="POST" action="{{ $route }}" enctype="multipart/form-data">
    @csrf
    @isset($method)
        @method($method)
    @endisset
    <div class="row">
        <div class="col-12 col-md-6 col-lg-5">

            {{ $person->byline ?? old('byline') }}

            <x-forms.input type="text" name="name" label="Name" class="mb-3" value="{{ $person->name ?? old('name') }}" />

            <x-forms.input type="text" name="byline" label="Byline" class="mb-3" value="{{ $person->byline ?? old('byline') }}" />

            <x-forms.input type="text" name="job_type" label="Job Type" class="mb-3" value="{{ $person->job_type ?? old('job_type') }}" />

            <x-forms.input type="url" name="website" label="Website" class="mb-3" value="{{ $person->website ?? old('website') }}" />

            <div class="mb-3">
                <p class="h4 text-tertiary mb-0">Social Media</p>
                <p class="text-muted small mb-1">
                    Handles only -- do not include the full URL
                </p>
                <x-forms.input-group type="text" name="facebook" label="https://www.facebook.com/" class="mb-1" value="{{ $person->facebook ?? old('facebook') }}" />
                <x-forms.input-group type="text" name="twitter" label="https://www.twitter.com/" class="mb-1" value="{{ $person->twitter ?? old('twitter') }}" />
                <x-forms.input-group type="text" name="linkedin" label="https://www.linkedin.com/" class="mb-1" value="{{ $person->linkedin ?? old('linkedin') }}" />
                <x-forms.input-group type="text" name="instagram" label="https://www.instagram.com/" class="mb-1" value="{{ $person->instagram ?? old('instagram') }}" />
                <x-forms.input-group type="text" name="google_scholar" label="https://scholar.google.com/citations?user=" class="mb-1" value="{{ $person->google_scholar ?? old('google_scholar') }}" />

            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-5">
            <div class="form-floating mb-3">
                <textarea class="form-control floating-textarea-xl" id="bio" name="bio" placeholder="Bio">{{ $person->bio ?? old('bio') }}</textarea>
                <label for="bio">Bio</label>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label"><x-entities.show.field-heading title="Photo" /></label>
                <input class="form-control" type="file" name="image" id="image">
            </div>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>
