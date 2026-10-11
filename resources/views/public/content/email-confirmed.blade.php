@extends('layouts.public')

@php
    // /confirmed stays crawlable (GA4 counts a view of it as a conversion) but out of the index. Whatever the stored CMS
    // head HTML holds, the page prints exactly one robots tag (noindex, follow), no canonical, one title and one description.
    $confirmedHead = $seo ? (string) $seo->seo : '';
    $confirmedHead = preg_replace([
        '/<meta\s+name="robots"[^>]*>\s*/i',
        '/<link\s+rel="canonical"[^>]*>\s*/i',
        '/<title>\s*<\/title>\s*/i',
        '/<meta\s+name="description"\s+content="\s*"[^>]*>\s*/i',
    ], '', $confirmedHead);
    $confirmedHasTitle = (bool) preg_match('/<title>/i', $confirmedHead);
    $confirmedHasDescription = (bool) preg_match('/<meta\s+name="description"/i', $confirmedHead);

    if ($seo) {
        $seo->seo = $confirmedHead;
    }
@endphp

@push('head')
    <meta name="robots" content="noindex, follow" />
    @unless($confirmedHasDescription)
        <meta name="description" content="Your email is confirmed and you are on the Spannr list. The Weekly Brief arrives every Thursday." />
    @endunless
    @if($seo && ! $confirmedHasTitle)
        <title>Subscription confirmed - Spannr</title>
    @endif
@endpush

@unless($seo)
    @section('title', 'Subscription confirmed - Spannr')
@endunless

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
