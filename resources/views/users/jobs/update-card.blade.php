@extends('layouts.user')

@push('head')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ Vite::asset('resources/js/stripe-setup-intent.js') }}" defer></script>
@endpush

@section('content')
    <div class="container text-center py-5">
        <h1 class="text-primary">Update Card</h1>
        <p class="fs-3 mb-1">Monthly Job Listing</p>
        <p class="fs-4 fw-bold text-quaternary">$99/month</p>
        <div>
            <div class="fw-bold text-uppercase small">Current Card:</div>
            <div>{{ $job->jobSubscription->card_name }}</div>
        </div>

        <div class="max-width-600 mx-auto my-5">
            <div id="stripe-checkout-data" class="my-5"
                 data-checkout-url="{{ route('users.jobs.setup-intent', [$job->subscription_id, $user->stripe_customer_id]) }}"
                 data-return-url="{{ route('users.jobs.update-card-confirmation', $job->id) }}">
                <form id="payment-form">
                    @csrf
                    <div id="loading-message">
                        <div class="text-body-secondary fs-5">Stripe's payment form is loading...</div>
                        <div>
                            <div class="small mb-2">If it doesn't load in 2-3 seconds, click the button below:</div>
                            <button type="button" id="load-job-subscription-update-card-form" class="btn btn-sm btn-quintary">Load the Form</button>
                        </div>
                    </div>
                    <div id="payment-element"></div>
                    <div class="my-4">
                        <button id="submit" class="btn btn-success btn-lg visually-hidden">
                            <span class="spinner visually-hidden" id="spinner">
                                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                            </span>
                            <span id="button-text">Update Card</span>
                        </button>
                    </div>
                    <div id="payment-message" class="hidden fs-4 text-tertiary fw-bold text-uppercase"></div>
                </form>
            </div>
        </div>
    </div>
@endsection
