@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-primary">Add Organization</h1>

        @include('admin.includes.status-messages')

        <div class="row">
            <div class="col-12 col-md-6 col-lg-5">
                <form action="{{ route('organizations.store') }}" method="post">
                    @csrf

                    <div class="mb-3">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}">
                    </div>
                    <div class="mb-3">
                        <label for="type">Type</label>
                        <select class="form-select" name="type" id="type">
                            <option selected disabled>Select type</option>
                            @foreach (\App\Models\Organization::TYPES as $type)
                                <option value="{{ $type }}" @if(old('type') == $type) selected @endif>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="website">Website</label>
                        <input type="url" class="form-control" name="website" id="website" value="{{ old('website') }}" placeholder="https://website.com">
                    </div>
                    <div class="mb-3">
                        <label for="founded_date">Year Founded</label>
                        <input type="number" class="form-control" name="founded_date" id="founded_date" value="{{ old('founded_date') }}" min="1700" max="<?php echo date('Y'); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="summary">Summary</label>
                        <textarea class="form-control" id="summary" name="summary" rows="3">{{ old('summary') }}</textarea>
                    </div>
                    <div>
                        <input type="submit" class="btn btn-primary" value="Save">
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

