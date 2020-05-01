<?php

class Impact {

  public $datetime;
  public $latitude;
  public $longitude;

  function __construct($datetime, $latitude, $longitude)
  {
    $this->datetime = $datetime;
    $this->latitude = $latitude;
    $this->longitude = $longitude;
  }

}
