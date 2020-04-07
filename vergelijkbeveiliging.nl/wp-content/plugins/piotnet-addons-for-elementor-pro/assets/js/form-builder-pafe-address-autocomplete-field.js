function pafeAddressAutocompleteInitMap() {
    var inputs = document.querySelectorAll('[data-pafe-form-builder-address-autocomplete]');
    inputs.forEach(function(el, index, array){
        var autocomplete = new google.maps.places.Autocomplete(el);
        var $mapSelector = el.closest('.elementor-element').querySelectorAll('[data-pafe-form-builder-address-autocomplete-map]');
        if($mapSelector.length>0) {
            var map = new google.maps.Map($mapSelector[0], {
                center: {lat: -33.8688, lng: 151.2195},
                zoom: 13
            });

            var infowindow = new google.maps.InfoWindow();
            var infowindowContent = el.closest('.elementor-element').querySelectorAll('.infowindow-content')[0];
            infowindow.setContent(infowindowContent);
            var marker = new google.maps.Marker({
              map: map,
              anchorPoint: new google.maps.Point(0, -29)
            });

            autocomplete.addListener('place_changed', function() {
              infowindow.close();
              marker.setVisible(false);
              var place = autocomplete.getPlace();
              if (!place.geometry) {
                // User entered the name of a Place that was not suggested and
                // pressed the Enter key, or the Place Details request failed.
                window.alert("No details available for input: '" + place.name + "'");
                return;
              }

              // If the place has a geometry, then present it on a map.
              if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
              } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);  // Why 17? Because it looks good.
              }
              marker.setPosition(place.geometry.location);
              marker.setVisible(true);

              var address = '';
              if (place.address_components) {
                address = [
                  (place.address_components[0] && place.address_components[0].short_name || ''),
                  (place.address_components[1] && place.address_components[1].short_name || ''),
                  (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
              }

              infowindowContent.children['place-icon'].src = place.icon;
              infowindowContent.children['place-name'].textContent = place.name;
              infowindowContent.children['place-address'].textContent = address;
              infowindow.open(map, marker);
            });
        }
    });
}
