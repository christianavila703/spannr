@extends('layouts.public')

@if($seo)
    @php
        // Keep /confirmed crawlable (GA4 conversion page) but out of the index.
        // Drop the stored robots tag so only one robots directive is printed.
        $seo->seo = preg_replace('/<meta\s+name="robots"[^>]*>\s*/i', '', (string) $seo->seo);
    @endphp
    @push('head')
        <meta name="robots" content="noindex, follow" />
    @endpush
@endif

@section('content')
    <div class="page-container">
        <div class="container">
            <div class="single-page-content confirmed-page mx-auto max-width-600 py-4 py-md-5">
                <h1 class="text-center mb-3">You're in. Nice.</h1>

                <p class="lead text-center">Thanks for confirming. You're officially on the Spannr list, and I'm glad you're here.</p>

                <p class="text-center">The Weekly Brief hits your inbox every Thursday at 7 am MST.</p>

                <h2 class="h4 text-center mt-5 mb-4">Three quick things while you wait</h2>

                <ol class="ps-3">
                    <li class="mb-3">
                        <a href="{{ route('public.content.articles.show', 'longevity-doctors') }}"><strong>Explore Longevity Cities.</strong></a>
                        Our guides to longevity doctors and clinics, city by city, with what they cost and how to choose.
                    </li>
                    <li class="mb-3">
                        <a href="{{ route('public.marketplace.index') }}"><strong>Poke around the directory.</strong></a>
                        900+ clinics, docs, coaches and retreats.
                    </li>
                    <li class="mb-3">
                        <strong>Whitelist us.</strong>
                        Add <strong class="text-break">spannr@mail.beehiiv.com</strong> to your contacts so the brief lands in your inbox and not in promotions.
                    </li>
                </ol>

                <p class="mt-5">If something feels off or you've got a question, email me at <a href="mailto:brent@spannr.com">brent@spannr.com</a>. It comes straight to me.</p>

                <p class="fw-bold mb-0">Brent</p>
            </div>
        </div>
    </div>
@endsection
