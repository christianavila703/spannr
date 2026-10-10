@extends('layouts.public')

@push('seo-extra')
{!! \App\Support\StructuredData::forListing($listing) !!}
@endpush

@section('content')
    @include('public.marketplace.content._content-hero')
    <div class="container my-5">
        <x-nav.breadcrumbs parentUrl="{{ route('public.marketplace.index') }}" parentLabel="Longevity Marketplace" currentLabel="{{ $listing->name }}"/>
        <hr class="dark mb-5">
        @if($canEditListing)
            <div class="mb-4">
                <a href="#edit" class="btn btn-lg btn-quaternary text-white">
                    <i class="fa-sharp fa-solid fa-pen-to-square"></i> Edit Listing
                </a>
            </div>
            @if($listing->type === \App\Models\Listing::TYPE_CLINIC OR \App\Models\Listing::TYPE_DOCTOR)
                <livewire:public.marketplace.show-listing.medical-provider :listing="$listing" />
                <div id="edit" class="mt-5 border-top border-2 border-dark pt-4">
                    <livewire:public.marketplace.listing-forms.medical-provider :listing="$listing" :canEditUsers="$canEditUsers"/>
                </div>
            @endif
        @else
            <div>
                @if($listing->type === \App\Models\Listing::TYPE_CLINIC OR \App\Models\Listing::TYPE_DOCTOR)
                    @include("public.marketplace.listing-data.medical-provider")
                @endif

                @if($listing->users_count === 0)
                    <livewire:public.marketplace.claim-listing :listing="$listing" />
                @endif
            </div>

        @endif

        @include('public.marketplace._city-links')
    </div>
@endsection


@push('end')
    @vite('resources/js/marketplace-listing-map.js')
    <style>
        #listing-map {
            height: 300px;
        }
    </style>

    @if($canEditListing)
        @vite('resources/js/tinymce-listing.js')
    @endif
@endpush
