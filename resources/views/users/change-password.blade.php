@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="text-primary">Settings</h1>

        @include('admin.includes.status-messages')

        <div class="row">
            <div class="col-12 col-md-8 col-lg-6">
                <form action="{{ route('users.store-password') }}" method="post">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-12 col-md-6">
                            <label for="currentPassword">Current Password</label>
                            <input type="password" name="currentPassword" id="currentPassword" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <label for="password">New Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="password_confirm">Confirm Password</label>
                            <input type="password" name="password_confirm" id="password_confirm" class="form-control" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

