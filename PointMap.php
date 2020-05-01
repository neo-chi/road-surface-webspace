<!DOCTYPE html>

<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>Point Map</title>
    <style>
        /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
        #map {
            height: 100%;
        }

        /* Optional: Makes the sample page fill the window. */
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<html>

<body>
    <?php include 'navbar.php';?>
    <div id="map"></div>

    <script>
        // var customLabel = {
        //     0: {
        //         label: 'NP'
        //     },
        //     1: {
        //         label: 'P'
        //     }
        // };
        var customColor = {
            0: {
                color: 'http://maps.google.com/mapfiles/ms/icons/green.png'
            },
            1: {
                color: 'http://maps.google.com/mapfiles/ms/icons/caution.png'
            }
        };

        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                center: new google.maps.LatLng(33.496458, -86.809695),
                zoom: 12
            });
            var infoWindow = new google.maps.InfoWindow;

            // Change this depending on the name of your PHP or XML file
            downloadUrl('http://roadreporter.us/test/map/converttoXML.php', function (data) {
                var xml = data.responseXML;
                var markers = xml.documentElement.getElementsByTagName('marker');
                Array.prototype.forEach.call(markers, function (markerElem) {
                    var id = markerElem.getAttribute('id');
                    var datetime = markerElem.getAttribute('datetime');
                    var ispothole = markerElem.getAttribute('ispothole');
                    var point = new google.maps.LatLng(
                        parseFloat(markerElem.getAttribute('lat')),
                        parseFloat(markerElem.getAttribute('lng')));

                    var infowincontent = document.createElement('div');
                    var strong = document.createElement('strong');
                    strong.textContent = datetime
                    infowincontent.appendChild(strong);
                    infowincontent.appendChild(document.createElement('br'));

                    var text = document.createElement('text');
                 // text.textContent = ispothole
                 // infowincontent.appendChild(text);
                 // var icon = customLabel[ispothole] || {};
                    var pin = customColor[ispothole] || {};
                    var marker = new google.maps.Marker({
                        map: map,
                        position: point,
                    //  label: icon.label,
                        icon: pin.color
                    });
                    marker.addListener('click', function () {
                        infoWindow.setContent(infowincontent);
                        infoWindow.open(map, marker);
                    });
                });
            });
        }



        function downloadUrl(url, callback) {
            var request = window.ActiveXObject ?
                new ActiveXObject('Microsoft.XMLHTTP') :
                new XMLHttpRequest;

            request.onreadystatechange = function () {
                if (request.readyState == 4) {
                    request.onreadystatechange = doNothing;
                    callback(request, request.status);
                }
            };

            request.open('GET', url, true);
            request.send(null);
        }

        function doNothing() { }
    </script>
    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0nQNGIwjoXMtoXKO8nd6puPKIrXPMKtk&libraries=visualization&callback=initMap">
        </script>
</body>

</html>