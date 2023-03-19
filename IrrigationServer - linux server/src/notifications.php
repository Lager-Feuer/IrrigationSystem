<?php
require_once("/var/www/private/irrigationServer/vendor/autoload.php");
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

function sendNotifications($mysqli, $moistureLevel, $triggerValue)
{
    $mysqli =
    $stmt = $mysqli->prepare("SELECT * FROM PNSubscriptions");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result != null) {
        while ($row = mysqli_fetch_assoc($result)) {
            $keys = [
                "p256dh" => $row['p256dh'],
                "auth" => $row['auth']
            ];

            $sub_data = json_encode([
                "endpoint" => $row['endpoint'],
                "expirationTime" => $row['expirationTime'],
                "keys" => $keys
            ]);

            $sub2 = json_decode($sub_data, true);
            $sub = Subscription::create($sub2);

            // Set VAPID-Keys and email in push-object
            $push = new WebPush(["VAPID" => [
                "subject" => "<EMAIL>",
                "publicKey" => "<PUBLIC-KEY>",
                "privateKey" => "<PRIVATE-KEY>"
            ]]);

            // Send notification to clients
            $resultNotification = $push->sendOneNotification($sub, json_encode([
                "title" => "Ihr Bewässerungssystem",
                "body" => "Alert: Feuchtigkeit ist unter $triggerValue%. Aktueller Wert: $moistureLevel%. Please take necessary measures to maintain optimal humidity levels."
            ]));
        }
    }
    mysqli_stmt_close($stmt);
}

?>