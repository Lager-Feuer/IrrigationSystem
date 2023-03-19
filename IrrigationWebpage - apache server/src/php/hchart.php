<?php
require_once('/var/www/private/priv.php');
header('Content-Type: application/json');

$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if(!$mysqli){
  die("Connection failed: " . $mysqli->error);
}

$fromTimestamp = $_GET['fromTimestamp'];
$toTimestamp = $_GET['toTimestamp'];

if(!$fromTimestamp || !$toTimestamp) die("Timestamp missing");

$stmt = $mysqli->prepare("SELECT * FROM chart_data WHERE timestamp > ? AND timestamp < ?");
$stmt->bind_param("ss", $fromTimestamp, $toTimestamp);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
$i = 1;

foreach ($result as $row) {
  if ($i % 10 == 0) {
    $data[] = $row;
  }
  $i++;
}

$result->close();
mysqli_stmt_close($stmt);
$mysqli->close();

print_r( json_encode($data));
?>