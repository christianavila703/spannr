@extends('layouts.app')

@section('content')
    <div class="container mb-5">
        @include('admin.includes.status-messages')

        <div class="row mb-4">
            <div class="col-12 d-flex flex-row-reverse">
                <a class="btn btn-primary confirm-button" href="{{ route('admin.form-entries.resubmit-mailchimp') }}">Re-submit to Mailchimp</a>
            </div>
        </div>

        <h1 class="text-primary h4">Website Contact Forms</h1>
        <livewire:admin.form-entries.website-contact-index />

        <h1 class="text-primary h4 mt-5">Investor Opt-in Forms</h1>
        <livewire:admin.form-entries.investor-opt-in-index />

        <h1 class="text-primary h4 mt-5">Funding Applicant Forms</h1>
        <livewire:admin.form-entries.funding-applicant-index />

        <h1 class="text-primary h4 mt-5">Newsletter Subscribers</h1>
        <livewire:admin.form-entries.newsletter-subscribers />
    </div>
@endsection
