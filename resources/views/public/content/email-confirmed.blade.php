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
            <div class="single-page-content">
                <p class="text-center">
                    <strong>Confirmed</strong>. Check your inbox for an email from admin@spannr.com.
                </p>

                <h2 class="title-medium my-4 mx-auto max-width-600 text-center">
                    Looking to invest in the longevity ecosystem?
                </h2>

                <p class="text-center">Investors can apply here to join our syndicate and see our deals.</p>

                <livewire:forms.investor-opt-in />
            </div>
        </div>
    </div>
@endsection
