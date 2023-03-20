@extends('layouts.public')

@section('content')
    <div class="page-jobs">
        <div class="single-post-wrapper">
            <div class="container pt-5 pt-lg-6 pb-5">
                <h1>Submit a Longevity Job</h1>
                <p class="mb-3 h5 text-quaternary">Are you hiring in the longevity industry? We'd love to help you find the right candidate.</p>
                <hr class="dark mb-4">
                <div class="d-flex flex-column flex-lg-row">
                    <div class="flex-grow-1">
                        <livewire:public.jobs.organization-select />
                        <livewire:public.jobs.submit-job-posting />
                    </div>
                    <div class="d-none d-xl-block ms-xl-5 flex-shrink-0 submit-job-sidebar">
                        <div class="bg-light p-4">
                            <p class="lead">We're passionate about longevity, and want to help you find the best candidates for your organization.</p>
                            <h3 class="h5">Spannr Job Posting</h3>
                            <ul class="m-0">
                                <li>Featured on our jobs board</li>
                                <li>Featured in a weekly newsletter</li>
                                <li>Shared on Twitter &amp; LinkedIn</li>
                            </ul>
                            <p class="m-0 fw-bold lead ms-3 mt-2">$99 / month</p>
                            <hr class="dark my-4">
                            <h3 class="h5">Spannr Recruitment Team</h3>
                            <p class="m-0">Work with the Spannr recruitment team to find the perfect candidate. Our professional recruiters will work with your company by accessing our vast database of longevity specific professionals. Whether you're hiring for one specific job or an entire team, we can handle the exhausting process of finding the right candidate.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('end')
    @vite('resources/js/tinymce-jobs.js')
    @vite('resources/sass/geosearch-autocomplete.scss')
    <script src="https://cdn.jsdelivr.net/npm/@opencage/geosearch-bundle" type="text/javascript"></script>
    @vite('resources/js/job-submission-location.js')
@endpush
