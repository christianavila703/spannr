@extends('layouts.public')

@section('content')
    <div class="page-container">
        <div class="container">
            <div class="single-page-content">
                <h1 class="text-center">Contact Us</h1>
                <p class="lead text-center max-width-600 mx-auto">We'd love to hear from you! Fill out the form below and we'll get back to you as soon as possible.</p>

                <div class="pb-5">
                    <livewire:forms.contact />
                </div>
            </div>
        </div>
    </div>
@endsection
