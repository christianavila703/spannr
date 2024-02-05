import 'leaflet/dist/leaflet.css';
import L from "leaflet";

import markerIcon from "/node_modules/leaflet/dist/images/marker-icon-2x.png";
L.Marker.prototype.setIcon(L.icon({
    iconUrl: markerIcon,
    iconSize: [25, 41],
    popupAnchor: [0, -25],
}));

window.addEventListener('load-listing-map', event => {
    try {
        loadMap();
    } catch(error) {

    }
});

window.onload = function() {
    loadMap();
};

function loadMap() {
    if (document.getElementById('listing-map')) {
        let mapElement = document.getElementById('listing-map');
        let latitude = mapElement.getAttribute('data-latitude');
        let longitude = mapElement.getAttribute('data-longitude');
        let name = mapElement.getAttribute('data-name');
        let address = mapElement.getAttribute('data-address');

        let listingMap = L.map('listing-map').setView([latitude, longitude], 13);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 15,
            attribution: '© OpenStreetMap'
        }).addTo(listingMap);

        let listingMarker = L.marker([latitude, longitude]).addTo(listingMap);

        listingMarker.bindPopup("<strong>" + name + "</strong><br>" + address).openPopup();
    }
}

if (document.getElementById("map-anchor-link")) {
    let mapAnchorLinkElement = document.getElementById("map-anchor-link");
    let listingMapElement = document.getElementById('listing-location-map')
    let headerOffset = 55;
    let elementPosition = listingMapElement.getBoundingClientRect().top;
    let scrollPosition = elementPosition + window.pageYOffset - headerOffset;

    mapAnchorLinkElement.addEventListener('click', function() {
        window.scrollTo({
         top: scrollPosition,
            behavior: "smooth"
        });
    });
}
