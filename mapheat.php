<!DOCTYPE html>
<html>
<head>
</head>
<body>

<!-- Generate some test data for TRAVEL LOG -->
<?php

  // Include Travel data transfer object class and transfer to document object model function.
  include_once 'libraries/DTO/Travel.php';
  include_once 'libraries/toDOM.php';
  
  $travelArray = [];

  for ($i = 0; $i < 200; $i++) {

    $latitude = 33 + (0.001 * $i);
    $longitude = -86 + (0.001 * $i);
    $travel = new Travel($latitude, $longitude);
    array_push($travelArray, $travel);

  }

  //$travel_0 = new Travel(33.000001, -86.000001);
  //$travel_1 = new Travel(33.001002, -86.001001);
  //$travel_2 = new Travel(33.002001, -86.002001);
  //$travel_3 = new Travel(33.003001, -86.003001);
  //$travel_4 = new Travel(33.004001, -86.004001);

  // Transfer objects to DOM for JS retrieval.
  for ($i = 0; $i < sizeof($travelArray); $i++) {

    toDOM($travelArray[$i]);

  }

  //toDOM($travel_0);
  //toDOM($travel_1);
  //toDOM($travel_2);
  //toDOM($travel_3);
  //toDOM($travel_4);

?>

<!-- Include library to read log data from DOM -->
<script type="text/javascript" src="libraries/fromDOM.js"></script>
<script type="text/javascript" src="libraries/googleMapsLatLngFactory.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC0nQNGIwjoXMtoXKO8nd6puPKIrXPMKtk&libraries=visualization"></script>

<script>

  var travelLog = fromDOM('TRAVEL');
  console.log("TRAVEL LOG");
  console.log(travelLog);
  console.log(googleMapsLatLngFactory(travelLog));

</script>

</body>
</html>