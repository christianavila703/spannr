@extends('layouts.public')

@section('content')
    <div class="single-post-wrapper">
        <div class="container">
            <div class="single-post-content-container">
                <livewire:articles.index />
            </div>
            <div id="sidebar" class="sidebar">
                <p>Join us for everything you need to know about the quest to extend healthy human lifespans.</p>
                @include('public.includes.mailchimp-sidebar')
            </div>
        </div>
    </div>
@endsection
