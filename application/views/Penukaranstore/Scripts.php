<script>
    const x = document.getElementById("demo");

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(postPosition);
        } else {
            x.innerHTML = "Geolocation is not supported by this browser.";
        }
    }

    function postPosition(position) {
        // x.innerHTML = "Latitude: " + position.coords.latitude +
        //     "<br>Longitude: " + position.coords.longitude;
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;
        $.ajax({
            type: "POST",
            dataType: "json",
            url: window.location.origin + "/salesorder/penukaranstore/add_latlong",
            data: {
                lat: latitude,
                long: longitude
            },
            success: function(data) {
                window.location.replace(window.location.origin + "/salesorder/penukaranstore/list");
            }
        });
    }
</script>