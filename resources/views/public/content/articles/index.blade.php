@extends('layouts.public')

@section('content')
    <div class="single-post-wrapper">
        <div class="container pt-5 pt-lg-6 pb-5">
            <h1>Insights</h1>
            <p class="mb-3 h5 text-quaternary">Everything you need to know about all things longevity</p>
            <hr class="dark mb-4">
            <div class="single-post-content-container">
                <livewire:articles.index />
            </div>
        </div>
    </div>
@endsection
