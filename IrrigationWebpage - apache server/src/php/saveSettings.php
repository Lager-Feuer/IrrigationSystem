<?php
require_once('/var/www/private/priv.php');
header('Content-Type: application/json');

$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if(!$mysqli){
  die("Connection failed: " . $mysqli->error);
}

$syncInterval = $_POST['syncInterval'];
$lowerTriggerValue = $_POST['lowerTriggerValue'];
$upperTriggerValue = $_POST['upperTriggerValue'];
$notificationTriggerValue = $_POST['notificationTriggerValue'];
$maintenanceActive = (int)$_POST['maintenanceActive'];
$pump1Force = (int)$_POST['pump1Force'];
$pump2Force = (int)$_POST['pump2Force'];
$testNotifications = (int)$_POST['testNotifications'];

$stmt = $mysqli->prepare("UPDATE clients SET syncInterval = ?, lowerTriggerValue = ?, upperTriggerValue = ?,  notificationTriggerValue = ?, maintenanceActive = ?, pump1Force = ?, pump2Force = ?, testNotifications = ? WHERE clientID = 1");
$stmt->bind_param("iiiiiiii", $syncInterval, $lowerTriggerValue, $upperTriggerValue, $notificationTriggerValue, $maintenanceActive, $pump1Force, $pump2Force, $testNotifications);
$stmt->execute();
if(mysqli_stmt_affected_rows($stmt) > 0) echo "Saved settings";
else echo "Error while saving";

mysqli_stmt_close($stmt);
$mysqli->close();
?>