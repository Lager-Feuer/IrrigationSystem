<?php
require_once('/var/www/private/priv.php');

// Prepare variables to store subscription in database
$sub_data = json_decode($_POST["sub"], true);
$endpoint = strval($sub_data['endpoint']);
$expirationTime = strval($sub_data['expirationTime']);
$p256dh = strval($sub_data['keys']['p256dh']);
$auth = strval($sub_data['keys']['auth']);

$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

if(!$mysqli){
  die("Connection failed: " . $mysqli->error);
}

// Check if Device is already subscribed to notifications
$stmt = $mysqli->prepare("SELECT * FROM PNSubscriptions WHERE endpoint = ?");
$stmt->bind_param("s", $endpoint);
$stmt->execute();
$result = $stmt->get_result();

if(mysqli_num_rows($result) != 0) die("This device is already subscribed to notifications.");
mysqli_stmt_close($stmt);

// Save subscription in database
$stmt = $mysqli->prepare("INSERT INTO PNSubscriptions(endpoint, expirationTime, p256dh, auth) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $endpoint, $expirationTime, $p256dh, $auth);
$stmt->execute();

if(mysqli_stmt_affected_rows($stmt) > 0) echo "This device will now receive notifications.";
else echo "Error while saving your subscription.";

mysqli_stmt_close($stmt);
$mysqli->close();
?>
