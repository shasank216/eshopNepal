<!DOCTYPE html>
<html>
<head>
    <title>Live Driver Location</title>
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
        html, body {
            height: 100%;
            margin: 0;
        }
    </style>
</head>
<body>
    <h2>Live Driver Location</h2>
    <div id="map"></div>

    <script>
    let map, marker;

    // Extract `id` from URL like: ?id=100149
    function getOrderIdFromUrl() {
        const params = new URLSearchParams(window.location.search);
        return params.get('id');
    }

    const orderId = getOrderIdFromUrl();

    if (!orderId) {
        alert("Order ID is missing in URL. Example: ?id=123");
    }

    // Initialize Google Map
    window.initMap = function () {
        const defaultLatLng = { lat: 27.7, lng: 85.3 };

        map = new google.maps.Map(document.getElementById("map"), {
            center: defaultLatLng,
            zoom: 14,
        });

        marker = new google.maps.Marker({
            position: defaultLatLng,
            map: map,
            title: "Driver Location",
        });

        // Start fetching location periodically
        if (orderId) {
            fetchAndUpdateLocation(); // Initial fetch
            setInterval(fetchAndUpdateLocation, 10000); // every 10 seconds
        }
    };

    // Fetch driver's latest location from your Laravel API
    function fetchAndUpdateLocation() {
        fetch(`/web-track-driver-location?order_id=${orderId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Location data:", data);
                if (data.latitude && data.longitude) {
                    const newLatLng = {
                        lat: parseFloat(data.latitude),
                        lng: parseFloat(data.longitude),
                    };
                    marker.setPosition(newLatLng);
                    map.setCenter(newLatLng);
                } else {
                    console.warn("Latitude or longitude missing in response", data);
                }
            })
            .catch(error => {
                console.error("Error fetching location:", error);
            });
    }
</script>

    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDumdDv9jxmpC0yaURPXnqkk4kssB8R3C4&callback=initMap">
    </script>
</body>
</html>
