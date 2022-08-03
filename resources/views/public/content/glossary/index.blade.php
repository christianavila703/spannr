@extends('layouts.public')

@section('content')
    <main class="page-glossary">
        <nav class="glossary-nav mx-auto">
            <ul class="glossary-nav-menu mx-auto">
                <li class="toggle-mobile"><span>+</span></li>
                @foreach ($alphabet as $letter)
                    <li>
                        <a href="#{{ $letter }}">{{ $letter }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>
        <div class="single-post-wrapper">

            <div class="container">
                <div class="single-post-content-container">
                    <h1 class="archive-title">Glossary</h1>
                    <div class="font-size-lg">
                        <p>Welcome to the spannr glossary, where we have collected and defined the most common words and terms in the world of longevity.</p>
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
            </div>
        </div>
    </main>
@endsection

@push('end')
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script>
    const nav = document.querySelector(".glossary-nav");
    const navMenu = document.querySelector('.glossary-nav-menu');

    function glossaryMenu(){

        if (window.scrollY >= nav.offsetTop){
            navMenu.classList.add("fixed-nav");
        } else {
            navMenu.classList.remove("fixed-nav");
        }
    }

    function glossaryMenuHeight() {
        let navHeight = document.querySelector(".glossary-nav-menu").offsetHeight;
        nav.style.height = navHeight + 'px';
    }
    window.addEventListener("scroll", glossaryMenu);
    window.onresize = glossaryMenuHeight;

    /* jQuery */
    jQuery(document).ready(function($) {
        /* Anchor Scrolling */
        $(document).on('click', 'a[href^="#"]', function (event) {
            event.preventDefault();
            $('html, body').animate({
                scrollTop: $($.attr(this, 'href')).offset().top + -100
            }, 1000);
        });

        /* Glossary Nav for Mobile */
        $('.glossary-nav-menu .toggle-mobile').on('click', function() {

            if ($(navMenu).hasClass('open')) {
                $(navMenu).removeClass('open');
                $('.glossary-nav-menu .toggle-mobile span').text('+');
            } else {
                $(navMenu).addClass('open');
                $('.glossary-nav-menu .toggle-mobile span').html('&ndash;');
            }

        });
    });
</script>
@endpush
