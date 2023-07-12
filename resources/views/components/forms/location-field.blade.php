<div>
    <div id="autocomplete"></div>
    <input type="hidden" name="name" id="locationName" value="{{ isset($location) ? $location->name : null }}">
    <input type="hidden" name="latitude" id="locationLatitude" value="{{ isset($location) ? $location->latitude : null }}">
    <input type="hidden" name="longitude" id="locationLongitude" value="{{ isset($location) ? $location->longitude : null }}">
</div>

@push('end')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@opencage/geosearch-bundle/dist/css/autocomplete-theme-classic.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/@opencage/geosearch-bundle" type="text/javascript"></script>

<script>
    const locationSearchOptions = {
        key: "oc_gs_7hkjbkvjnshvonvcsu8zv7eh0q823v",
        language: 'en'
    };

    const locationNameField = document.getElementById('locationName');
    const locationLatitudeField = document.getElementById('locationLatitude');
    const locationLongitudeField = document.getElementById('locationLongitude');

    const handleLocationSearchResult = ({ item }) => {
        console.log("Selected Item is", item);

        let locationName = item.formatted;
        let latitude = item.geometry.lat;
        let longitude = item.geometry.lng;

        locationNameField.value = locationName;
        locationLatitudeField.value = latitude;
        locationLongitudeField.value = longitude;

        console.log("locationName: " + locationName);
        console.log("latitude: " + latitude);
        console.log("longitude: " + longitude);
    };

    const locationSearchEvents = {
        onSelect: handleLocationSearchResult
    };

    opencage.algoliaAutocomplete({
        container: "#autocomplete",
        placeholder: "Search for places",
        plugins: [opencage.OpenCageGeoSearchPlugin(locationSearchOptions, locationSearchEvents)]
    });
</script>
@endpush
