@extends('layouts.public')

@include('public.content._seo-include')

@section('content')
    @include('public.marketplace.content._content-hero')
    <div class="container mb-5">
        <div class="d-lg-flex max-width-1200 justify-content-between mx-auto">
            <div class="max-width-800 px-xl-5">
                <div class="my-4">
                    <x-nav.breadcrumbs parentUrl="{{ route('public.marketplace.index') }}" parentLabel="Marketplace" currentLabel="Red Light Therapy"/>
                </div>
                <div>{!! $content->content !!}</div>
                @include('public.marketplace.content.red-light-therapy._cta')
                <div class="marketplace-cold-plunge-text-content">{!! $content->sources !!}</div>
                @include('public.marketplace.content.red-light-therapy._cta')
            </div>
            <div class="marketplace-sidebar-content ps-lg-5">
                <div class="mb-5 max-width-400 mt-lg-5">
                    <img src="https://spannr.com/storage/media/140/red-light-therapy-help-longevity.jpg" alt="Find red light therapy near your location">
                </div>
                <div class="border border-dark p-3 pt-2">
                    <h2 class="sidebar-title border-bottom border-dark">Why Choose Red Light Therapy</h2>
                    <ul class="list-with-checkmarks">
                        <li>
                            <div class="icon">
                                <i class="fa-sharp fa-solid fa-plus fa-fw"></i>
                            </div>
                            <div>
                                <strong>No Equipment</strong>
                                <span>Just show up</span>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <i class="fa-sharp fa-solid fa-plus fa-fw"></i>
                            </div>
                            <div>
                                <strong>In and Out</strong>
                                <span>No setup or cleanup</span>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <i class="fa-sharp fa-solid fa-plus fa-fw"></i>
                            </div>
                            <div>
                                <strong>Budget Friendly</strong>
                                <span>No investments in expensive devices</span>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <i class="fa-sharp fa-solid fa-plus fa-fw"></i>
                            </div>
                            <div>
                                <strong>Established Research</strong>
                                <span>Red Light Therapy is FDA approved</span>
                            </div>
                        </li>
                        <li>
                            <div class="text-tertiary">
                                <i class="fa-sharp fa-solid fa-arrow-right fa-fw text-tertiary"></i>
                            </div>
                            <div>
                                <a href="{{ route('public.marketplace.index', ['search' => 'red light therapy']) }}">Find Red Light Therapy</a>
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
                        <x-optins.newsletter showNameFields="true" position="other" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('public.marketplace.content._join')
@endsection
