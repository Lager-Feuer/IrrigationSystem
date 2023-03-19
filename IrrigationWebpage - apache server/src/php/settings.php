<?php
require_once('/var/www/private/priv.php');
header('Content-Type: application/json');

$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if(!$mysqli){
  die("Connection failed: " . $mysqli->error);
}

$stmt = $mysqli->prepare("SELECT lowerTriggerValue, upperTriggerValue, syncInterval, notificationTriggerValue, pump1Activated, pump2Activated, lastSentMoistureLvl, maintenanceActive, pump1Force, pump2Force, testNotifications FROM clients WHERE clientID = 1");
$stmt->execute();
$result = $stmt->get_result();

$data = array();
foreach ($result as $row) {
  $data[] = $row;
}

$result->close();
mysqli_stmt_close($stmt);
$mysqli->close();

print_r( json_encode($data));
?>