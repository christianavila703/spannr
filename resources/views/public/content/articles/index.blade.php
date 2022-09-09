@extends('layouts.public')

@section('content')
    <div class="single-post-wrapper">
        <div class="container">
            <div class="single-post-content-container">
                <livewire:articles.index />
            </div>
            <div id="sidebar" class="sidebar">
                <div class="gray-newsletter-optin">
                    <x-optins.newsletter showNameFields="true" />
                </div>
            </div>
        </div>
    </div>
@endsection
