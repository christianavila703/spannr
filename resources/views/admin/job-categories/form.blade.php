@push('head')
    @trixassets
@endpush

<form method="POST" action="{{ $route }}" enctype="multipart/form-data">
    @csrf
    @isset($method)
        @method($method)
    @endisset

    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <x-forms.input type="text" name="name" label="Name" class="mb-3" value="{{ $jobCategory->name ?? old('name') }}" />

            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
</form>
