@extends('layouts.public')

@section('content')
    <main class="page-spannr-event-confirmed">
        <div class="container text-center py-5 text-dark-gray">
            <h1 class="title-medium mb-3 text-tertiary">Thanks for posting your job!</h1>

            <p class="h4 max-width-600 mx-auto mb-5 line-space-lg">
                Please check your inbox for receipt, and we'll be following up with more details shortly!
            </p>

            <div class="text-center pt-5">
                <a href="{{ route('users.jobs.index') }}" class="btn btn-dark px-4 rounded-0">View &amp; Manage Jobs</a>
            </div>
        </div>
    </main>
@endsection
