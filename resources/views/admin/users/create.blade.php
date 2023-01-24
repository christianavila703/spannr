@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-primary">Create a User</h1>

        @include('admin.includes.status-messages')

        <form action="{{ route('admin.users.store') }}" method="POST" class="max-width-420">
            @csrf
            <div class="mb-3 d-flex">
                <input type="text" class="form-control me-2" name="name" aria-label="First Name" placeholder="First Name" value="{{ old('name') }}">
                <input type="text" class="form-control ms-2" name="last_name" aria-label="Last Name" placeholder="Last Name" value="{{ old('last_name') }}">
            </div>
            <input type="email" class="form-control mb-3" name="email" aria-label="Email" placeholder="Email" value="{{ old('email') }}">
            <input type="text" class="form-control mb-3" name="byline" aria-label="Author Byline" placeholder="Author Byline" value="{{ old('byline') }}">
            <input type="password" class="form-control mb-3" name="password" aria-label="Password" placeholder="Password">
            <strong class="d-block">Role(s)</strong>
            <div class="mb-3 d-flex flex-wr align-items-center">
                @foreach($roles as $role)
                    <div class="form-check form-check-inline me-3">
                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role-{{ $role->id }}">
                        <label class="form-check-label" for="role-{{ $role->id }}">{{ $role->name }}</label>
                    </div>
                @endforeach
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
@endsection
