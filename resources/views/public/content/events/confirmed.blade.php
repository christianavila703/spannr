@extends('layouts.public')

@section('content')
    @include('public.content.events._header')
    <main class="page-spannr-event-confirmed">
        <div class="container text-center py-5 text-dark-gray">
            <h1 class="title-medium mb-3 text-tertiary">Thank you for your purchase.</h1>

            <p class="h4 max-width-600 mx-auto mb-5 line-space-lg">
                Please check your inbox for receipt, and we'll be following up with more details shortly!
            </p>

            @include('public.content.events._sd-banner')

            <div class="text-center pt-5">
                <a href="/" class="btn btn-dark px-4 rounded-0">Back to Spannr</a>
            </div>
        </div>
    </main>
@endsection
