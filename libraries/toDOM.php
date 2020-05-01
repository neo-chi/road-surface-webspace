<?php
include_once '/DTO/Impact.php';
include_once '/DTO/Travel.php';

function toDOM($object)
{
  // Check what type of object is being exported. 
  if ($object instanceof Impact) {

    define('IMPACT_LOG', '<div class="impact-log">');
    define('END',        '</div>');

    $impact = $object;
    echo IMPACT_LOG;
    echo '{"datetime":"' . $impact->datetime . '","latitude":' . $impact->latitude . ',"longitude":' . $impact->longitude . '}';
    echo END;

  } else if ($object instanceof Travel) {

    define('TRAVEL_LOG', '<div class="travel-log">');
    define('END',        '</div>');

    $travel = $object;
    echo TRAVEL_LOG;
    echo '{"latitude":' . $travel->latitude . ',"longitude":' . $travel->longitude . '}';
    echo END;

  }

}
