@extends('layouts.public')

@push('head')
    {!! $graph !!}
@endpush

@section('content')
    <div class="container pt-5 about-user-page">
        <div class="d-lg-flex">
            <div class="flex-shrink-0 me-5">
                <img src="@if ($user->team_photo) {{ $user->team_photo }} @else {{Vite::asset('resources/images/team/missing.jpg')}} @endif" alt="{{ $user->name }}, {{ $user->title }}"
                     width="200" height="200" class="mb-4 mb-lg-0">
                @if ($user->person)
                    <div class="row mt-2">
                        <div class="lead text-primary d-flex social-icons">
                            @if ($user->person->facebook)
                                <a class="me-2" href="{{ $user->person->facebook_url }}" target="_blank" rel="noopener noreferrer" class="text-tertiary">
                                    <i class="fa-brands fa-facebook"></i>
                                </a>
                            @endif
                            @if ($user->person->twitter)
                                <a class="me-2" href="{{ $user->person->twitter_url }}" target="_blank" rel="noopener noreferrer" class="text-tertiary">
                                    <i class="fa-brands fa-twitter"></i>
                                </a>
                            @endif
                            @if ($user->person->linkedin)
                                <a class="me-2" href="{{ $user->person->linkedin_url }}" target="_blank" rel="noopener noreferrer" class="text-tertiary">
                                    <i class="fa-brands fa-linkedin"></i>
                                </a>
                            @endif
                            @if ($user->person->instagram)
                                <a class="me-2" href="{{ $user->person->instagram_url }}" target="_blank" rel="noopener noreferrer" class="text-tertiary">
                                    <i class="fa-brands fa-instagram"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            <div>
                <h1>{{ $user->full_name }}</h1>
                <h2 class="h5 text-quaternary">{{ $user->title }}</h2>
                <p class="max-width-800 mb-0">{{ $user->bio }}</p>
            </div>
        </div>
        <div class="mt-5 mb-6">
            <h2 class="h1">Articles</h2>
            <hr class="dark mb-5">

            <div class="article-cards-container">
                <livewire:users.user-articles :user="$user" />
            </div>

        </div>
    </div>
@endsection
