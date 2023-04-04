@extends('layouts.public')

@section('content')
    <div class="page-jobs">
        <div class="single-post-wrapper">
            <div class="container pt-5 pt-lg-6 pb-5">
                @if($job)
                    <h1>Apply for {{ $job->name }}</h1>
                    <p class="mb-3 h5 text-quaternary">Fill out the form below to apply for this job.</p>
                @else
                    <h1>Longevity Industry Recruiting</h1>
                    <p class="mb-3 h5 text-quaternary">Spannr’s recruiting team will help you find the best job opportunities in longevity</p>
                    <p class="mb-3 max-width-800">Fill out your information and we’ll find jobs that best fit your background. This service comes at no expense to you, the applicant, and we will not share your information with any parties outside of Spannr Jobs service without your permission.</p>
                @endif
                <hr class="dark mb-4">
                <livewire:public.jobs.apply :job="$job" />
            </div>
        </div>
    </div>
@endsection

@push('end')
    @vite('resources/sass/geosearch-autocomplete.scss')
    <script src="https://cdn.jsdelivr.net/npm/@opencage/geosearch-bundle" type="text/javascript"></script>
    @vite('resources/js/job-applicant-location.js')
@endpush
