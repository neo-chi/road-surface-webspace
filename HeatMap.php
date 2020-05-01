<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Heatmap</title>
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

    #floating-panel {
      position: absolute;
      top: 10px;
      left: 25%;
      z-index: 5;
      background-color: #fff;
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
      left: 25%;
      padding: 5px;
      position: absolute;
      top: 10px;
      z-index: 5;
    }
  </style>
</head>

<!-- Navigation Menu -->
<?php include 'navbar.php' ?>

<body>

  <!-- Map Controls -->
  <div id="floating-panel">
    <button onclick="toggleHeatmap()">Toggle Heatmap</button>
    <button onclick="changeGradient()">Change gradient</button>
    <button onclick="changeRadius()">Change radius</button>
    <button onclick="changeOpacity()">Change opacity</button>
  </div>
  <div id="map"></div>

  <!-- Include Google Maps API -->
  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0nQNGIwjoXMtoXKO8nd6puPKIrXPMKtk&libraries=visualization">
  </script>

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

    echo '<pre>';
    var_dump($key);
    var_dump($value);
    echo '</pre>';

  }


  // Generate test Travel data objects.
  //$travelArray = [];
  //for ($i = 0; $i < 500; $i++) {

  //$latitude = 33.5 + (0.001 * $i * 2);
  //$longitude = -86.8 + (0.001 * $i* 3);
  //$travel = new Travel($latitude, $longitude);
  //array_push($travelArray, $travel);

  //}

  //// Transfer objects to DOM for JS retrieval.

  //toDOM($travelArray[$i]);

  //}

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


    /** 
     * @brief Initialize the Google Maps background.
     */
    function initMap(_zoom, _center, _mapTypeId) {

      map = new google.maps.Map(document.getElementById('map'), {

        zoom: 12,
        center: {
          lat: 33.497083,
          lng: -86.809330
        },
        mapTypeId: 'roadmap'

      });

      heatmap = new google.maps.visualization.HeatmapLayer({

        data: getPoints(),
        map: map,
        dissipating: true,
        radius: 20,
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


      if (heatmap.get('radius') == 20) {
        heatmap.set('radius', 10);
      } else if (heatmap.get('radius') == 10) {
        heatmap.set('radius', 20);
      }


    }

    function changeOpacity() {


      if (heatmap.get('opacity') == 1) {
        heatmap.set('opacity', 0.5);
      } else if (heatmap.get('opacity') == 0.5) {
        heatmap.set('opacity', 1);
      }


    }
  </script>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0nQNGIwjoXMtoXKO8nd6puPKIrXPMKtk&libraries=visualization&callback=initMap">
  </script>
</body>

</html>