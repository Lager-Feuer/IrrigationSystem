<?php
require_once('/var/www/private/priv.php'); // Only DB constants
require_once('notifications.php');
require_once('dbStmts.php');
require_once('socket.php');

// Start database connection & terminate script on error
$mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
if (!$mysqli) die("Connection with database failed: " . $mysqli->error);

// Start socket connection
$serverSocket = createServerSocket("<IP>", <PORT>);
$sentNotifications = false;

while ($clientSocket = stream_socket_accept($serverSocket, -1)) {
    echo "Found possible client\n";

    $data = fgets($clientSocket);
    echo "received: " . $data . "\n";


    // Check, if there are any devices in the database
    $deviceName = getClientsValue($mysqli, "deviceName");
    $applicationKey = getClientsValue($mysqli, "applicationKey");
    if(!$deviceName || !$applicationKey)
    {
        closeAllConns("No capable devices in the database.", $mysqli, $clientSocket, $serverSocket);
    }

    // Check, if sent deviceName and applicationKey equal those in the database
    $jsonData = json_decode($data, true);
    if(strcmp($jsonData["deviceName"], $deviceName) !== 0 || strcmp($jsonData['applicationKey'], $applicationKey) !== 0)
    {
        closeAllConns("Invalid authentication.", $mysqli, $clientSocket, $serverSocket);
    }

    while(true)
    {
        sendCommandToClient($clientSocket, "getData");
        $response = json_encode(['status' => 'success', 'command' => 'getData']);
        fwrite($clientSocket, $response);

        // Get settings from database
        $syncInterval = getClientsValue($mysqli, "syncInterval");
        $lowerTrigger = getClientsValue($mysqli, "lowerTriggerValue");
        $upperTrigger = getClientsValue($mysqli, "upperTriggerValue");
        $notificationTrigger = getClientsValue($mysqli, "notificationTriggerValue");
        $maintenanceActive = getClientsValue($mysqli, "maintenanceActive");
        $pump1Force = getClientsValue($mysqli, "pump1Force");
        $pump2Force = getClientsValue($mysqli, "pump2Force");
        $testNotifications = getClientsValue($mysqli, "testNotifications");

        $data = fgets($clientSocket);
        echo $data . "\n";
        $jsonData = json_decode($data, true);

        $pump1Activated = $jsonData["pump1Activated"];
        $pump2Activated = $jsonData["pump2Activated"];
        $humidityPercentage = $jsonData["moistureLevel"] / 700 * 100;
        $humidityPercentage = number_format($humidityPercentage, 2, '.', '');
        
        saveTimestamp($mysqli, $humidityPercentage);
        updatePumpStatuses($mysqli, (int)$pump1Activated, (int)$pump2Activated);
        updateLastSent($mysqli, $humidityPercentage);

        if($maintenanceActive == 1)
        {
            if($pump1Force == 1 && !$pump1Activated) 
            {
                sendCommandToClient($clientSocket, "activatePump1");
                echo "Sent activate command -> pump 1\n";
            }
            if($pump1Force == 0 && $pump1Activated) 
            {
                sendCommandToClient($clientSocket, "deactivatePump1");
                echo "Sent deactivate command -> pump 1\n";
            }
            if($pump2Force == 1 && !$pump2Activated) 
            {
                sendCommandToClient($clientSocket, "activatePump2");
                echo "Sent activate command -> pump 2\n";
            }
            if($pump2Force == 0 && $pump2Activated) 
            {
                sendCommandToClient($clientSocket, "deactivatePump2");
                echo "Sent deactivate command -> pump 2\n";
            }

            if($testNotifications == 1) sendNotifications($mysqli, $humidityPercentage, $notificationTrigger);
            sleep($syncInterval);
            continue;
        }

        if( $humidityPercentage < $lowerTrigger && !$pump1Activated )
        {
            sendCommandToClient($clientSocket, "activatePump1");
            echo "Sent activate command\n";
        }
        
        if( $humidityPercentage > $upperTrigger && $pump1Activated)
        {
            sendCommandToClient($clientSocket, "deactivatePump1");
            $sentNotifications = false;
            echo "sent deactivate command\n";
        }

        // Send notifications if humidity level is below defined value
        if ($humidityPercentage < $notificationTrigger && !$sentNotifications)
        {
            $sentNotifications = true;
            sendNotifications($mysqli, $humidityPercentage, $notificationTrigger);
            echo "sent notifications\n";
        }
            sleep($syncInterval);
    }
}

/*
Humidity-measurement converted into percentage
100% = 800 -> 90% = 720  80% = 640
70% = 560   60% = 480   50% = 400
40% = 320   30% = 240   20% = 160
10% = 80    5% = 40     0%
*/

// Close the sockets and database connection
closeAllConns("cleanup", $mysqli, $client_socket, $serverSocket);
function closeAllConns($message, $mysqli = null, $clientSocket = null, $serverSocket = null)
{
    if($mysqli != null ) $mysqli->close();
    if($clientSocket != null ) fclose($clientSocket);
    if($serverSocket != null ) fclose($serverSocket);
    die($message);
}

?>