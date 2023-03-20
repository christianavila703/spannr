window.addEventListener('loadJobSubmissionLocationSearch', event => {
     const locationSearchOptions = {
        key: "oc_gs_7hkjbkvjnshvonvcsu8zv7eh0q823v",
        language: 'en'
    };

    const handleLocationSearchResult = ({ item }) => {
        let locationName = item.formatted;
        let latitude = item.geometry.lat;
        let longitude = item.geometry.lng;
        console.log(locationName + latitude + longitude);
        Livewire.emit('updateJobLocation', locationName, latitude, longitude);
    };

    const locationSearchEvents = {
        onSelect: handleLocationSearchResult
    };

    opencage.algoliaAutocomplete({
        container: "#autocomplete",
        placeholder: "Search for places",
        plugins: [opencage.OpenCageGeoSearchPlugin(locationSearchOptions, locationSearchEvents)]
    });

});
