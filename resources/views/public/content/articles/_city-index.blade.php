{{--
    DEV-9: "Longevity Doctors by City". Included only on the /articles/longevity-doctors page (see show.blade.php).
    Lists the published city guides, grouped by state, from App\Support\CityGuideIndex.
--}}
@php($cityGroups = \App\Support\CityGuideIndex::grouped())
@if(count($cityGroups))
    <section class="city-guide-index mt-5" id="longevity-doctors-by-city" aria-labelledby="city-guide-index-title">
        <h2 class="h3 mb-3" id="city-guide-index-title">Longevity Doctors by City</h2>
        @foreach($cityGroups as $groupName => $cities)
            <h3 class="h5 mt-4 mb-2">{{ $groupName }}</h3>
            <ul class="list-unstyled mb-0">
                @foreach($cities as $city)
                    <li class="mb-1"><a href="{{ $city['url'] }}">{{ $city['city'] }}</a></li>
                @endforeach
            </ul>
        @endforeach
    </section>
@endif
