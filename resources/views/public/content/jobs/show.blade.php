@extends('layouts.public')

@section('content')
    <div class="single-post-wrapper">
        <div class="container">
            <div class="single-post-content-container pb-5">
                <x-nav.breadcrumbs parentUrl="{{ route('public.content.jobs.index') }}" parentLabel="Jobs" currentLabel="{{ $job->name }}"/>
                <hr class="dark mb-5">
                <h1>{{ $job->name }}</h1>
                <div class="max-width-800">
                    <div class="mt-4">
                        <x-content.jobs.job-summary :job="$job" />
                    </div>
                    <div class="mt-5">
                        <h2 class="h4 border-bottom border-dark">Job Description</h2>
                        <div>
                            {!! $job->trixRender('description') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
