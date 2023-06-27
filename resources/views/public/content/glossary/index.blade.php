@extends('layouts.public')

@section('subheader')
    <nav class="glossary-nav mx-auto">
        <ul class="glossary-nav-menu mx-auto">
            <li class="toggle-mobile">
                <span class="closed">+</span>
                <span class="opened">&ndash;</span>
            </li>
            @foreach ($alphabet as $letter)
                <li>
                    <a href="#{{ $letter }}">{{ $letter }}</a>
                </li>
            @endforeach
        </ul>
    </nav>
@endsection

@section('content')
    <main class="page-glossary">
        <div class="single-post-wrapper pt-3">
            <div class="single-post-content-container">
                <div class="glossary-post">
                    <div class="single-post-content-sidebar-container">
                        <article class="single-post-content pb-6">
                            <div class="post-content">
                                <h1 class="archive-title">Glossary</h1>
                                <div class="font-size-lg">
                                    <p>Welcome to the Spannr glossary, where we have collected and defined the most common words and terms in the world of longevity.</p>
                                </div>

                                <div class="glossary-list">
                                    @foreach ($glossary as $group => $glossaryItems)
                                        <div class="glossary-list-group">
                                            <h2 id="{{ $group }}" class="group-title">{{ $group }}</h2>

                                            @foreach ($glossaryItems as $glossaryItem)
                                                <div class="glossary-list-group-item">
                                                    <a href="{{ route('public.content.glossary.show', $glossaryItem['slug']) }}">
                                                        <h3 class="title">{{ $glossaryItem['name'] }}</h3>
                                                        <div class="excerpt">{{ $glossaryItem['excerpt'] }}</div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </article>
                        <div class="sidebar-container glossary-sidebar">
                            <div id="sidebar" class="sidebar">
                                <div id="sticky-stabilizer" style="height: 20px;"></div>
                                <div id="sticky-sidebar-contents">
                                    <div class="outline-newsletter-optin">
                                        <x-optins.newsletter showNameFields="true" />
                                    </div>
                                    <div class="mt-4">
                                        <livewire:public.random-glossary-item-widget />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="after-single-post"></div>
    </main>
@endsection

@push('end')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    @vite('resources/js/sticky-sidebar.js')
    @vite('resources/js/glossary.js')
@endpush
