@extends('layouts.public')

@include('public.content._seo-include')

@section('content')
    @include('public.marketplace._hero')
    @include('public.marketplace._nav')
    <div class="container text-start my-5">
        <p class="marketplace-intro-text max-width-780 mx-auto">Find longevity services and treatments to increase your healthspan and extend your lifespan.</p>
        <livewire:public.marketplace.marketplace-index />
    </div>

    @include('public.marketplace.content._join')

    <script>
        let marketplaceSearchPresets = document.getElementById('marketplace-search-preset-options');
        let marketplaceSearchBox = document.getElementById('marketplace-search-box');

        marketplaceSearchBox.addEventListener('focusin', function(){
            marketplaceSearchPresets.classList.remove('d-none');
        });

        marketplaceSearchBox.addEventListener('change', function(){
            if (marketplaceSearchBox.value) {
                marketplaceSearchPresets.classList.add('d-none');
            } else {
                marketplaceSearchPresets.classList.remove('d-none');
            }
        });

        window.addEventListener('hide-marketplace-search-presets', event => {
            marketplaceSearchPresets.classList.add('d-none');
        });
    </script>
@endsection
