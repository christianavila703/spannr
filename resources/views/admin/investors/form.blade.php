<form method="POST" action="{{ $route }}">
    @csrf
    @isset($method)
        @method($method)
    @endisset
    <div class="row">
        <div class="col-12 col-md-6 col-lg-5">

            <x-forms.input type="text" name="name" label="Name" class="mb-3" value="{{ $investor->name ?? old('name') }}" />

            <div class="form-floating mb-3">
                <select class="form-select" id="type" name="type" aria-label="Type of investor">
                    <option selected disabled>Select Type</option>
                    @foreach (\App\Models\Investor::TYPES as $type)
                        <option value="{{ $type }}"
                                @isset($investor)
                                @if($investor->type == $type) selected @endif
                                @else
                                @if(old('type') == $type) selected @endif
                            @endisset
                        >{{ $type }}</option>
                    @endforeach
                </select>
                <label for="type">Type of investor</label>
            </div>

            <x-forms.input type="url" name="website" label="Website" class="mb-3" value="{{ $investor->website ?? old('website') }}" />

            <div class="form-floating mb-3">
                <textarea class="form-control floating-textarea-sm" id="summary" name="summary" placeholder="Summary">{{ $investor->summary ?? old('summary') }}</textarea>
                <label for="summary">Tagline / Summary</label>
            </div>
            <div class="mb-3">
                <p class="h4 text-tertiary mb-0">Social Media</p>
                <p class="text-muted small mb-1">
                    Handles only -- do not include the full URL
                </p>
                <x-forms.input-group type="text" name="facebook" label="https://www.facebook.com/" class="mb-1" value="{{ $investor->facebook ?? old('facebook') }}" />
                <x-forms.input-group type="text" name="twitter" label="https://www.twitter.com/" class="mb-1" value="{{ $investor->twitter ?? old('twitter') }}" />
                <x-forms.input-group type="text" name="linkedin" label="https://www.linkedin.com/" class="mb-1" value="{{ $investor->linkedin ?? old('linkedin') }}" />
                <x-forms.input-group type="text" name="instagram" label="https://www.instagram.com/" class="mb-1" value="{{ $investor->instagram ?? old('instagram') }}" />

            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-5">
            <div class="form-floating mb-3">
                <textarea class="form-control floating-textarea-xl" id="description" name="description" placeholder="Description">{{ $investor->description ?? old('description') }}</textarea>
                <label for="description">Description / Content Area</label>
            </div>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>
