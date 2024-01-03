@extends('layouts.user')

@section('content')
    <div class="container text-center py-5">
        <h1 class="text-primary">Update Card</h1>
        <p class="fs-3 mb-1">Monthly Job Listing</p>
        <p class="fs-4 fw-bold text-quaternary">$99/month</p>

        <div class="my-5 text-center">
            <livewire:users.jobs.update-card-status setupIntentId="{{ $setupIntent->id }}" />
        </div>
    </div>
@endsection
