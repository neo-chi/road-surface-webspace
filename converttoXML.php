<?php

include("libraries/db_conn_credentials.php");

function parseToXML($htmlStr)
{
$xmlStr=str_replace('<','&lt;',$htmlStr);
$xmlStr=str_replace('>','&gt;',$xmlStr);
$xmlStr=str_replace('"','&quot;',$xmlStr);
$xmlStr=str_replace("'",'&#39;',$xmlStr);
$xmlStr=str_replace("&",'&amp;',$xmlStr);
return $xmlStr;
}

// Opens a connection to a MySQL server
$connection=db_connect();
if (!$connection) {
  die('Not connected : ' . mysqli_error());
}

// Set the active MySQL database
$db_selected = mysqli_select_db($connection, dbs296492);
if (!$db_selected) {
  die ('Can\'t use db : ' . mysqli_error());
}

// Select all the rows in the markers table
$query = "SELECT * FROM LOG_DATA WHERE 1";
$result = mysqli_query($connection, $query);
if (!$result) {
  die('Invalid query: ' . mysqli_error());
}

header("Content-type: text/xml");

// Start XML file, echo parent node
echo "<?xml version='1.0' ?>";
echo '<markers>';
$ind=0;
// Iterate through the rows, printing XML nodes for each
while ($row = @mysqli_fetch_assoc($result)){
  // Add to XML document node
  echo '<marker ';
  echo 'id="' . $row['id'] . '" ';
  echo 'imageFileName="' . $row['imageFileName'] . '" ';
  echo 'datetime="' . parseToXML($row['datetime']) . '" ';
  echo 'lat="' . $row['latitude'] . '" ';
  echo 'lng="' . $row['longitude'] . '" ';
  echo 'acknowledged="' . $row['acknowledged'] . '" ';
  echo 'ai_classification="' . $row['ai_classification'] . '" ';
  echo 'ai_severity="' . $row['ai_severity'] . '" ';
  echo 'user_classification="' . $row['user_classification'] . '" ';
  echo 'user_severity="' . $row['user_severity'] . '" ';
  echo '/>';
 
  $ind = $ind + 1;
}

// End XML file
echo '</markers>';

?>