@extends('layouts.public')

@section('content')
    <main class="page-home">
        <section class="home-hero-container">
            <div class="container">
                <div class="content">
                    <img src="{{ Vite::asset('resources/images/spannr-logo-white.svg') }}" alt="spannr" class="logo">
                    <h1 class="title-large">End-to-End Longevity</h1>
                    <p>spannr is the world's most comprehensive source of longevity news, information and insights. <span class="mobile-only">Join us.</span></p>
                    <p class="desktop-only">Join us for everything you need to know about the quest to extend healthy human lifespans.</p>
                    <div class="mailchimp-form">
                        @include('public.includes.mailchimp')
                    </div>
                </div>
            </div>
        </section>

        <section class="home-content-container">
            <div class="container">
                <h2 class="section-title title-medium">spannr coverage empowers you to see and seize longevity opportunities as they happen.</h2>
                <div class="content">
                    <div class="image">
                        <img src="{{ Vite::asset('resources/images/exploding_photos.jpg') }}" alt="Photo collage representing increasing lifespans through education and data">
                    </div>
                    <div class="text">
                        <ul>
                            <li class="comprehensive">
                                <h3>Comprehensive</h3>
                                <p>Science, technology, business and investment</p>
                            </li>
                            <li class="uncomplicated">
                                <h3>Uncomplicated</h3>
                                <p>Complex science made simple and understandable</p>
                            </li>
                            <li class="independent">
                                <h3>Independent</h3>
                                <p>Objective reporting &amp; coverage with no hidden agenda</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section class="home-email-signup">
            <div class="container">
                <img src="{{ Vite::asset('resources/images/spannr-mark-white.svg') }}" alt="Spannr" class="logo-mark">
                <p class="title-medium">Get weekly insights on the most important news impacting the longevity industry.</p>
                <div class="text-center">
                    <div class="mailchimp-form mx-auto">
                        @include('public.includes.mailchimp')
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
