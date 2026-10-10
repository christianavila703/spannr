{{--
    DEV-8: neutral internal links under a clinic or doctor profile. Same-city listings of the same type, in
    alphabetical order (no ranking, nobody pays for placement), plus links to the city guide and the national guide.
    Nothing is shown for Coach and Retreat listings.
--}}
@php
    $cityLinksType = $listing->type;
    $cityLinksIsClinic = $cityLinksType === \App\Models\Listing::TYPE_CLINIC;
    $cityLinksIsDoctor = $cityLinksType === \App\Models\Listing::TYPE_DOCTOR;
@endphp
@if($cityLinksIsClinic || $cityLinksIsDoctor)
    @php
        $cityLinksLocation = \App\Support\ListingCityLinks::locationOf($listing);
        $cityLinksNext = $cityLinksLocation ? \App\Support\ListingCityLinks::nextInCity($listing) : collect();
        $cityLinksGuide = $cityLinksLocation ? \App\Support\ListingCityLinks::guideFor($listing) : null;
    @endphp
    <section class="listing-city-links mt-5 pt-4 border-top" aria-label="More in this city">
        @if($cityLinksNext->isNotEmpty())
            <h2 class="h4 mb-3">More longevity {{ $cityLinksIsClinic ? 'clinics' : 'doctors' }} in {{ $cityLinksLocation['city'] }}</h2>
            <ul class="list-unstyled mb-4">
                @foreach($cityLinksNext as $cityLinksListing)
                    <li class="mb-1"><a href="{{ $cityLinksListing->show_url }}">{{ $cityLinksListing->name }}</a></li>
                @endforeach
            </ul>
        @endif
        <ul class="list-unstyled mb-0">
            @if($cityLinksGuide)
                <li class="mb-1"><a href="{{ route('public.content.articles.show', $cityLinksGuide->slug) }}">Longevity doctors in {{ $cityLinksLocation['city'] }}</a></li>
            @endif
            <li><a href="{{ route('public.content.articles.show', \App\Support\CityGuideIndex::NATIONAL_SLUG) }}#longevity-doctors-by-city">All longevity doctors by city</a></li>
        </ul>
    </section>
@endif
