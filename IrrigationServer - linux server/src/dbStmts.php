<?php

function getClientsValue($mysqli, $columnKey)
{
    $stmt = $mysqli->prepare("SELECT * FROM clients WHERE clientID = 1");
    $stmt->execute();

    $result = $stmt->get_result();
    if (!$result) return null;

    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    return $row[$columnKey];
}

function saveTimestamp($mysqli, $humidityLevel)
{
    $stmt = $mysqli->prepare("INSERT INTO chart_data (id, humidity_level, timestamp) VALUES (NULL, ?, CURRENT_TIMESTAMP);");
    $stmt->bind_param("s", $humidityLevel);
    $stmt->execute();

    if(mysqli_stmt_affected_rows($stmt) > 0) $returnValue = 1;
    else $returnValue = -1;

    mysqli_stmt_close($stmt);

    return $returnValue;
}

function updatePumpStatuses($mysqli, $pump1Activated, $pump2Activated)
{

    $stmt = $mysqli->prepare("UPDATE clients SET pump1Activated = ?, pump2Activated = ? WHERE clients.clientID = 1");
    $stmt->bind_param("ii", $pump1Activated, $pump2Activated);
    $stmt->execute();

    if(mysqli_stmt_affected_rows($stmt) > 0) $returnValue = 1;
    else $returnValue = -1;

    mysqli_stmt_close($stmt);

    return $returnValue;
}

function updateLastSent($mysqli, $lastSent)
{

    $stmt = $mysqli->prepare("UPDATE clients SET lastSentMoistureLvl = ? WHERE clients.clientID = 1");
    $stmt->bind_param("i", $lastSent);
    $stmt->execute();

    if(mysqli_stmt_affected_rows($stmt) > 0) $returnValue = 1;
    else $returnValue = -1;

    mysqli_stmt_close($stmt);

    return $returnValue;
}

?>