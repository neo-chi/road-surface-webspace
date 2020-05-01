<!DOCTYPE html>

<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>Combined Maps</title>
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
            overflow: hidden;
        }

        #floating-panel {
            position: absolute;
            top: 10px;
            left: 25%;
            z-index: 5;
            background-color: #000;
            padding: 5px;
            border: 1px solid #999;
            text-align: center;
            font-family: 'Roboto', 'sans-serif';
            line-height: 30px;
            padding-left: 10px;
        }

        #floating-panel {
            background-color: #fff;
            border: 1px solid #999;
            left: 36%;
            padding: 5px;
            position: absolute;
            top: 10px;
            z-index: 5;
        }

        /* The popup form - hidden by default */
        .form-popup {
            display: none;
            position: fixed;
            bottom: 33px;
            left: 8px;
            border: 3px solid #f1f1f1;
            z-index: 9;
        }

        /* Add styles to the form container */
        .form-container {
            max-width: 300px;
            max-height: 900px;
            padding: 10px;
            background-color: white;
            overflow: scroll;
        }

        /* Full-width input fields */
        .form-container input[type=text],
        .form-container input[type=number],
        select {
            width: 80%;
            padding: 15px;
            margin: 5px 0 22px 0;
            border: none;
            background: #f1f1f1;
        }

        /* When the inputs get focus, do something */
        .form-container input[type=text]:focus,
        .form-container input[type=number]:focus,
        select {
            background-color: #ddd;
            outline: none;
        }

        /* Set a style for the submit/login button */
        .form-container .btn {
            background-color: #4CAF50;
            color: white;
            padding: 16px 20px;
            border: none;
            cursor: pointer;
            width: 100%;
            margin-bottom: 10px;
            opacity: 0.8;
        }

        /* Add a red background color to the cancel button */
        .form-container .cancel {
            background-color: red;
        }

        /* Add some hover effects to buttons */
        .form-container .btn:hover,
        .open-button:hover {
            opacity: 1;
        }
    </style>
</head>

<html>

<body>

    <!-- Map Controls -->
    <div id="floating-panel">
        <button onclick="toggleHeatmap()">Toggle Heatmap</button>
        <button onclick="changeGradient()">Change gradient</button>
        <button onclick="changeRadius()">Change radius</button>
        <button onclick="changeOpacity()">Change opacity</button>
        <button onclick="togglePins()">Toggle Pins</button>
    </div>

    <div class="form-popup" id="markerInfoForm">
        <form action="/submitForm.php" class="form-container" autocomplete="off">
            <h1 id="formHead">Details</h1>

            <label for="lat"><b><br>Latitude</b></label>
            <input type="text" placeholder="LAT" id="lat" readonly>

            <label for="lng"><b><br>Longitude</b></label>
            <input type="text" placeholder="LNG" id="lng" readonly>

            <label for="datetime"><b><br>Date/Time</b></label>
            <input type="text" placeholder="Date/Time" id="datetime" readonly>

            <label for="aiClass"><b><br>AI Classification</b></label>
            <select id="aiClass" value="" disabled>
                <option value=""></option>
                <option value="0">Non-Event</option>
                <option value="1">Pothole</option>
                <option value="2">Roadkill</option>
                <option value="3">Tree</option>
                <option value="4">Unknown</option>
            </select>

            <label for="aiSev"><b><br>AI Severity</b></label>
            <input type="text" placeholder="AI Severity" id="aiSev" readonly>

            <label for="userClass"><b><br>User Classification</b></label>
            <select id="userClass" value="" required>
                <option value=""></option>
                <option value="0">Non-Event</option>
                <option value="1">Pothole</option>
                <option value="2">Roadkill</option>
                <option value="3">Tree</option>
                <option value="4">Unknown</option>
            </select>

            <label for="userSev"><b><br>User Severity</b></label>
            <input type="number" placeholder="User Severity" id="userSev" min="0" max="10" required>

            <button type="submit" class="btn">Submit</button>
            <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
        </form>
    </div>

    <!-- Include Google Maps API -->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0nQNGIwjoXMtoXKO8nd6puPKIrXPMKtk&libraries=visualization">
    </script>

    <!-- <?php include 'navbar.php'; ?> -->

    <div id="map"></div>

    <!-- Generate some test data for TRAVEL LOG -->
    <?php

    // Include Travel data transfer object class and transfer to document object model function.
    include_once 'libraries/DTO/Travel.php';
    include_once 'libraries/dbGetTravelLog.php';
    include_once 'libraries/toDOM.php';

    $travelLog = getTravelLog();
    for ($i = 0; $i < sizeof($travelLog); $i++) {
        toDOM($travelLog[$i]);
    }

    foreach ($travelLog as $key => $value) {

        //echo '<pre>';
        //var_dump($key);
        //var_dump($value);
        // echo '</pre>';
    }

    ?>

    <!-- Include Data Reading libraries -->
    <script type="text/javascript" src="libraries/fromDOM.js"></script>
    <script type="text/javascript" src="libraries/googleMapsLatLngFactory.js"></script>

    <!-- Google Maps API Implementation -->
    <script>
        // Google maps background map.
        var map;
        // Google maps heatmap overlay.
        var heatmap;

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
                color: 'http://maps.google.com/mapfiles/kml/paddle/grn-blank-lv.png'
            },
            1: {
                color: 'http://maps.google.com/mapfiles/ms/icons/caution.png'
            }
        };

        var allMarkers = [];

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: new google.maps.LatLng(33.496458, -86.809695),
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.TOP_RIGHT,
                    mapTypeIds:['roadmap','satellite']
                },
                zoom: 12,
                gestureHandling: 'greedy'
            });
            var infoWindow = new google.maps.InfoWindow;

            // Change this depending on the name of your PHP or XML file
            downloadUrl('/test/map/converttoXML.php', function(data) {
                var xml = data.responseXML;
                markers = xml.documentElement.getElementsByTagName('marker');
                Array.prototype.forEach.call(markers, function(markerElem) {
                    var id = markerElem.getAttribute('id');
                    var datetime = markerElem.getAttribute('datetime');
                    var ispothole = markerElem.getAttribute('is_pothole');
                    var acknowledged = markerElem.getAttribute('acknowledged');
                    var aiClassification = markerElem.getAttribute('ai_classification');
                    var aiSeverity = markerElem.getAttribute('ai_severity');
                    var userClassification = markerElem.getAttribute('user_classification');
                    var userSeverity = markerElem.getAttribute('user_severity');
                    var lat = markerElem.getAttribute('lat');
                    var lng = markerElem.getAttribute('lng');
                    var point = new google.maps.LatLng(
                        parseFloat(markerElem.getAttribute('lat')),
                        parseFloat(markerElem.getAttribute('lng')));

                    var infowincontent = document.createElement('div');
                    var strong = document.createElement('strong');
                    strong.textContent = datetime
                    infowincontent.appendChild(strong);
                    infowincontent.appendChild(document.createElement('br'));

                    var text = document.createElement('text');
                    text.textContent = ispothole
                    infowincontent.appendChild(text);

                    // var icon = customLabel[ispothole] || {};
                    var pin = customColor[ispothole] || {};
                    var marker = new google.maps.Marker({
                        map: map,
                        position: point,
                        //  label: icon.label,
                        icon: pin.color
                    });
                    allMarkers.push(marker);

                    // marker.addListener('click', function() {
                    //     infoWindow.setContent(infowincontent);
                    //     infoWindow.open(map, marker);

                    // });

                    marker.addListener('click', function() {
                        document.getElementById("markerInfoForm").style.display = "none";

                        document.getElementById("formHead").innerHTML = "Details:" + id;
                        document.getElementById("lat").placeholder = lat;
                        document.getElementById("lng").placeholder = lng;
                        document.getElementById("datetime").placeholder = datetime;
                        document.getElementById("aiClass").value = aiClassification;
                        document.getElementById("aiSev").placeholder = aiSeverity;
                        document.getElementById("userClass").value = userClassification;
                        document.getElementById("userSev").placeholder = userSeverity;

                        document.getElementById("markerInfoForm").style.display = "block";
                    });

                    marker.addListener('click', function() {
                        map.setZoom(16);
                        map.setCenter(marker.getPosition());
                    });

                });

            });

            heatmap = new google.maps.visualization.HeatmapLayer({

                data: getPoints(),
                map: map,
                dissipating: true,
                radius: 10,
                opacity: 1
            });
        }

        /**
         * @brief Collect travel data from database and return array of new google.maps.LatLng(LAT,LNG) points.
         * @return array of google.maps.LatLng objects.
         */
        function getPoints() {
            var travelLog = fromDOM('TRAVEL');
            return googleMapsLatLngFactory(travelLog);
        }


        /**
         * @brief Toggle Heatmap overlay if the heatmap contains a google map object.
         */
        function toggleHeatmap() {

            const currentMap = heatmap.getMap();
            if (currentMap == null) {

                heatmap.set('map', map);

            } else if (currentMap == map) {

                heatmap.set('map', null);

            }

        }

        function togglePins(type) {
            //console.log(allMarkers);
            //console.log(markers[1].Marker.getVisible());
            if (allMarkers) {
                for (i in allMarkers) {
                    var visibility = (allMarkers[i].getVisible() == true) ? false : true;
                    allMarkers[i].setVisible(visibility);
                }
            }
        }

        function changeGradient() {

            var gradient = [
                'rgba(0, 255, 255, 0)',
                'rgba(0, 255, 255, 1)',
                'rgba(0, 191, 255, 1)',
                'rgba(0, 127, 255, 1)',
                'rgba(0, 63, 255, 1)',
                'rgba(0, 0, 255, 1)',
                'rgba(0, 0, 223, 1)',
                'rgba(0, 0, 191, 1)',
                'rgba(0, 0, 159, 1)',
                'rgba(0, 0, 127, 1)',
                'rgba(63, 0, 91, 1)',
                'rgba(127, 0, 63, 1)',
                'rgba(191, 0, 31, 1)',
                'rgba(255, 0, 0, 1)'
            ]

            heatmap.set('gradient', heatmap.get('gradient') ? null : gradient);
        }

        function changeRadius() {

            if (heatmap.get('radius') == 10) {
                heatmap.set('radius', 15);
            } else if (heatmap.get('radius') == 15) {
                heatmap.set('radius', 10);
            }

        }

        function changeOpacity() {
            if (heatmap.get('opacity') == 1) {
                heatmap.set('opacity', 0.5);
            } else if (heatmap.get('opacity') == 0.5) {
                heatmap.set('opacity', 1);
            }
        }

        function downloadUrl(url, callback) {
            var request = window.ActiveXObject ?
                new ActiveXObject('Microsoft.XMLHTTP') :
                new XMLHttpRequest;

            request.onreadystatechange = function() {
                if (request.readyState == 4) {
                    request.onreadystatechange = doNothing;
                    callback(request, request.status);
                }
            };

            request.open('GET', url, true);
            request.send(null);
        }

        function closeForm() {
            document.getElementById("markerInfoForm").style.display = "none";
        }

        function doNothing() {}
    </script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0nQNGIwjoXMtoXKO8nd6puPKIrXPMKtk&libraries=visualization&callback=initMap">
    </script>
</body>

</html>