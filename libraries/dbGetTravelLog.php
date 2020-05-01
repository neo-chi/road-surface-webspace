<?php

include_once 'DTO/Travel.php';

function getTravelLog() {

  $travelLog = [];

  $result = queryTravelLog("SELECT * FROM dbs296492.LOG_DATA WHERE ai_severity>0");
  $row_cnt = $result->num_rows;
  
  if ($result) {

    for ($i = 1; $i <= $row_cnt; $i++) {

      $row = $result->fetch_assoc();
      $id         = $row['id'];
      $datetime   = $row['datetime'];
      $latitude   = $row['latitude'];
      $longitude  = $row['longitude'];
      //$is_pothole = $row['is_pothole'];

      $_travelLogItem = new Travel($latitude, $longitude);
      array_push($travelLog, $_travelLogItem);
    }

  }

  $result->free();

    return $travelLog;

}

function queryTravelLog($sqlStatement) {

  $conn = getDatabaseConnection();
  $result = $conn->query($sqlStatement);
  $conn->close();
  return $result;

}

function getDatabaseConnection() {

  $hostname = "db5000303500.hosting-data.io";
  $username = "dbu167212";
  $password = "Goodpassword1!";
  $database = "dbs296492";

  /* Create connection */
  $conn = new mysqli($hostname, $username, $password, $database);

  /* Check connection */
  if ($conn->connect_error) {
      die("Database Connection Failed: " . $conn->connect_error);
  }

  return $conn;

}
