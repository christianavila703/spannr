{{--
    DEV-8: "Clinics and doctors listed in {City}" at the end of a city guide article (longevity-doctors-{city}-{st}).
    Every Public Clinic and Doctor in that city, alphabetical. Hidden when there are none. The article body is not touched.
--}}
@php($cityGuideListings = \App\Support\ListingCityLinks::forGuide($article->slug))
@if($cityGuideListings['listings']->isNotEmpty())
    <section class="city-guide-listings mt-5" aria-labelledby="city-guide-listings-title">
        <h2 class="h3 mb-3" id="city-guide-listings-title">Clinics and doctors listed in {{ $cityGuideListings['city'] }}</h2>
        <ul class="list-unstyled city-listings-columns mb-0">
            @foreach($cityGuideListings['listings'] as $cityGuideListing)
                <li class="mb-1"><a href="{{ $cityGuideListing->show_url }}">{{ $cityGuideListing->name }}</a></li>
            @endforeach
        </ul>
    </section>
@endif
