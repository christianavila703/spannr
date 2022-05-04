@extends('layouts.app')

@section('content')
    <form class="container">
        <h1 class="text-primary">Add Organization</h1>

        @include('admin.includes.status-messages')

        <form method="POST" action="{{ route('organizations.store') }}">
            @csrf
            <div class="row">
                <div class="col-12 col-md-6 col-lg-5">

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="{{ old('name') }}">
                        <label for="name">Name</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select" id="type" name="type" aria-label="Floating label select example">
                            <option selected disabled>Select Type</option>
                            @foreach (\App\Models\Organization::TYPES as $type)
                                <option value="{{ $type }}" @if(old('type') == $type) selected @endif>{{ $type }}</option>
                            @endforeach
                        </select>
                        <label for="type">Type of organization</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="url" class="form-control" name="website" id="website" value="{{ old('website') }}" placeholder="https://website.com">
                        <label for="website">Website</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" name="founded_date" id="founded_date" placeholder="Year Founded" value="{{ old('founded_date') }}" min="1700" max="<?php echo date('Y'); ?>">
                        <label for="founded_date">Year Founded</label>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control floating-textarea-sm" id="summary" name="summary" placeholder="Summary">{{ old('summary') }}</textarea>
                        <label for="summary">Tagline / Summary</label>
                    </div>
                    <div class="mb-3">
                        <p class="h4 text-tertiary">Social Media</p>
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="basic-addon3">https://www.facebook.com/</span>
                            <input type="text" class="form-control" name="facebook" id="facebook" aria-label="Facebook URL">
                        </div>
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="basic-addon3">https://www.twitter.com/</span>
                            <input type="text" class="form-control" name="twitter" id="twitter" aria-label="Twitter URL">
                        </div>
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="basic-addon3">https://www.linkedin.com/</span>
                            <input type="text" class="form-control" name="linkedin" id="linkedin" aria-label="LinkedIn URL">
                        </div>
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="basic-addon3">https://www.instagram.com/</span>
                            <input type="text" class="form-control" name="instagram" id="instagram" aria-label="Instagram URL">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="form-floating mb-3">
                        <textarea class="form-control floating-textarea-xl" id="description" name="description" placeholder="Description">{{ old('description') }}</textarea>
                        <label for="description">Description / Content Area</label>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary" formmethod="post" formaction="{{ route('organizations.store') }}">Save</button>
                </div>
            </div>
        </form>
    </div>
@endsection

