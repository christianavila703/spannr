window.addEventListener('loadMarketplaceListingLocationSearch', event => {
     const locationSearchOptions = {
        key: "oc_gs_7hkjbkvjnshvonvcsu8zv7eh0q823v",
        language: 'en'
    };

    const handleLocationSearchResult = ({ item }) => {
        let locationName = item.formatted;
        let latitude = item.geometry.lat;
        let longitude = item.geometry.lng;
        Livewire.emit('updateMarketplaceListingLocation', locationName, latitude, longitude);
    };

    const locationSearchEvents = {
        onSelect: handleLocationSearchResult
    };

    opencage.algoliaAutocomplete({
        container: "#autocomplete",
        placeholder: "Search for city/state",
        plugins: [opencage.OpenCageGeoSearchPlugin(locationSearchOptions, locationSearchEvents)]
    });

    document.querySelector('.aa-ClearButton').addEventListener('click', function() {
        Livewire.emit('clearSearchLocation');
    });

});
