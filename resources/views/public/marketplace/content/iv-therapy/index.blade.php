@extends('layouts.public')

@include('public.content._seo-include')

@section('content')
    @include('public.marketplace.content._content-hero')
    <div class="container mb-5">
        <div class="d-lg-flex max-width-1200 justify-content-between mx-auto">
            <div class="max-width-800 px-xl-5">
                <div class="my-4">
                    <x-nav.breadcrumbs parentUrl="{{ route('public.marketplace.index') }}" parentLabel="Marketplace" currentLabel="IV Therapy"/>
                </div>
                <div>{!! $content->content !!}</div>
                @include('public.marketplace.content.iv-therapy._cta')
                <div class="marketplace-cold-plunge-text-content">{!! $content->sources !!}</div>
                @include('public.marketplace.content.iv-therapy._cta')
            </div>
            <div class="marketplace-sidebar-content ps-lg-5">
                <div class="mb-5 max-width-400 mt-lg-5">
                    <img src="https://spannr.com/storage/media/253/iv-therapy-for-longevity.jpg" alt="Find IV Therapy clinics near your location">
                </div>
                <div class="border border-dark p-3 pt-2">
                    <h2 class="sidebar-title border-bottom border-dark">Why IV Therapy</h2>
                    <ul class="list-with-checkmarks">
                        <li>
                            <div class="icon">
                                <i class="fa-sharp fa-solid fa-plus fa-fw"></i>
                            </div>
                            <div>
                                <strong>Treat Deficiencies</strong>
                                <span>Increase vitamin &amp; nutrient absorption</span>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <i class="fa-sharp fa-solid fa-plus fa-fw"></i>
                            </div>
                            <div>
                                <strong>Cleanse Your Body</strong>
                                <span>Helps remove toxins and free radicals</span>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <i class="fa-sharp fa-solid fa-plus fa-fw"></i>
                            </div>
                            <div>
                                <strong>Trusted Treatment</strong>
                                <span>Rely on licensed medical professionals</span>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <i class="fa-sharp fa-solid fa-plus fa-fw"></i>
                            </div>
                            <div>
                                <strong>Widely Used</strong>
                                <span>Providers available in most cities</span>
                            </div>
                        </li>
                        <li>
                            <div class="text-tertiary">
                                <i class="fa-sharp fa-solid fa-arrow-right fa-fw text-tertiary"></i>
                            </div>
                            <div>
                                <a href="{{ route('public.marketplace.index', ['search' => 'IV therapy']) }}">Find IV Therapy</a>
                                <span>See the listings in your area</span>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="mt-5 max-width-400">
                    <livewire:public.random-glossary-item-widget />
                </div>
                <div class="mt-5 max-width-400">
                    <div class="outline-newsletter-optin">
                        <x-optins.newsletter showNameFields="true" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('public.marketplace.content._join')
@endsection
