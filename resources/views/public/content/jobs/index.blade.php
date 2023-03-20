@extends('layouts.public')

@section('content')
    <div class="page-jobs">
        <div class="single-post-wrapper">
            <div class="container pt-5 pt-lg-6 pb-5">
                <div class="d-xl-flex align-items-end">
                    <div class="me-xl-5 mb-4 flex-sh">
                        <h1>Longevity Industry Jobs</h1>
                        <p class="mb-0 h5 text-quaternary">Want to work in longevity? Here's {{ $jobsCount }} jobs you might like</p>
                    </div>
                    <div class="d-flex flex-wrap align-items-end flex-xl-nowrap ms-auto flex-grow-1 justify-content-xl-end">
                        <div class="border border-quaternary border-2 p-3 me-4 me-xl-5 mb-4">
                            <p class="h5 mb-3"><span class="text-nowrap">Are you hiring in the</span> <span class="d-block text-nowrap">longevity industry?</span></p>
                            <a href="{{ route('public.content.jobs.submit') }}" class="btn btn-tertiary">Find Candidates</a>
                        </div>
                        <div class="border border-quaternary border-2 p-3 mb-4">
                            <p class="h5 mb-3"><span class="text-nowrap">Looking to get hired in</span> <span class="d-block text-nowrap">the longevity industry?</span></p>
                            <a href="" class="btn btn-tertiary">Get Hired</a>
                        </div>
                    </div>
                </div>
                <hr class="dark mt-0 mb-4">
                <livewire:public.jobs.jobs-index />
            </div>
        </div>
    </div>
@endsection
