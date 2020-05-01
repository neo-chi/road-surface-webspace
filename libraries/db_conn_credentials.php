<?php

function db_connect()
{
    $hostname = "db5000303500.hosting-data.io";
    $database = "dbs296492";
    $username = "dbu167212";
    $password = "Goodpassword1!";
    // Create connection
    $conn = new mysqli($hostname, $username, $password, $database);

    // Test connection
    if ($conn->connect_error) {
        die("Database Connection Failed: " . $conn->connect_error);
    }

    // Return connection
    return $conn;
}
