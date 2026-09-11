// locationModule.js
export function initLocationFeature({
  detectLocationBtnId = "detect-location-btn",
  locationInputId = "select-delivery-location",
  savedAddressSelector = "#savedAddress",
  headerAddressSelector = ".headerDeliveryLocation",
  googleMapsApiKey = "YOUR_GOOGLE_MAPS_API_KEY", // Replace with your actual API key
}) {
  const detectLocationBtn = document.getElementById(detectLocationBtnId);
  const locationInput = document.getElementById(locationInputId);
  const savedAddress = document.querySelector(savedAddressSelector);

  // Initialize Google Maps Places Autocomplete
  const autocomplete = new google.maps.places.Autocomplete(locationInput);

  // Function to check if the location is in Delhi
  function isLocationInDelhi(addressComponents) {
    let isDelhi = true;
    for (let component of addressComponents) {
      // Check for Delhi in administrative_area_level_1 (state) or administrative_area_level_2 (district)
      if (
        component.types.includes("administrative_area_level_1") &&
        component.long_name.toLowerCase().includes("delhi")
      ) {
        isDelhi = true;
      }
    }
    return isDelhi;
  }

  // Function to show a delivery status message
  function showDeliveryStatus(isDeliverable) {
    if (isDeliverable) {
      savedAddress.innerHTML = `<span style="color: green;">We can deliver to this location!</span>`;
    } else {
      savedAddress.innerHTML = `<span style="color: red;">We cannot deliver to this location.</span>`;
    }
  }

  // Function to update the header with the location
  function headerDeliverStatus(address) {
    const headerAddress = document.querySelector(headerAddressSelector);
    headerAddress.innerHTML = address;
  }

  // Store location data in local storage
  function storeLocationData(address, latitude, longitude) {
    const locationData = {
      address: address,
      latitude: latitude,
      longitude: longitude,
    };
    localStorage.setItem("userLocation", JSON.stringify(locationData));
  }

  // Retrieve location data from local storage
  function loadLocationData() {
    const locationData = JSON.parse(localStorage.getItem("userLocation"));
    if (locationData) {
      locationInput.value = locationData.address;
      headerDeliverStatus(locationData.address); // Update the header with the stored address
      // const isDeliverable = isLocationInDelhi(locationData.address.split(","));
      const isDeliverable = true;
      showDeliveryStatus(isDeliverable);
    }
  }

  // Load the stored location data when the page loads
  loadLocationData();

  // Detect user's current location using latitude and longitude
  detectLocationBtn.addEventListener("click", function () {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function (position) {
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;
          const latlng = new google.maps.LatLng(lat, lng);
          const geocoder = new google.maps.Geocoder();

          // Use geocoder to get the address based on latitude and longitude
          geocoder.geocode(
            {
              location: latlng,
            },
            function (results, status) {
              if (status === "OK") {
                if (results[0]) {
                  const isDeliverable = isLocationInDelhi(
                    results[0].address_components
                  );
                  locationInput.value = results[0].formatted_address;
                  headerDeliverStatus(results[0].formatted_address); // Update header
                  showDeliveryStatus(isDeliverable);

                  // Store the location data in local storage
                  storeLocationData(results[0].formatted_address, lat, lng);
                } else {
                  alert("No results found");
                }
              } else {
                alert("Geocoder failed due to: " + status);
              }
            }
          );
        },
        function () {
          alert("Geolocation failed");
        }
      );
    } else {
      alert("Geolocation is not supported by this browser.");
    }
  });

  // Check location when Enter key is pressed
  locationInput.addEventListener("keydown", function (event) {
    if (event.key === "Enter") {
      event.preventDefault();
      const place = autocomplete.getPlace();
      if (place) {
        const isDeliverable = isLocationInDelhi(place.address_components);
        showDeliveryStatus(isDeliverable);
        headerDeliverStatus(place.formatted_address); // Update header

        // Store the location data in local storage
        storeLocationData(
          place.formatted_address,
          place.geometry.location.lat(),
          place.geometry.location.lng()
        );
      }
    }
  });

  // Check location when a place is selected from autocomplete
  autocomplete.addListener("place_changed", function () {
    const place = autocomplete.getPlace();
    if (place) {
      const isDeliverable = isLocationInDelhi(place.address_components);
      showDeliveryStatus(isDeliverable);
      headerDeliverStatus(place.formatted_address); // Update header

      // Store the location data in local storage
      storeLocationData(
        place.formatted_address,
        place.geometry.location.lat(),
        place.geometry.location.lng()
      );
    }
  });
}
