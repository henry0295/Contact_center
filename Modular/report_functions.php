// report_functions.php
<?php

include 'db_connection.php';

function getTotalDeliveredAndRejected($conn) {
    $sqlDelivered = "SELECT COUNT(*) AS total_delivered FROM reportes_voice WHERE evento = 'delivered'";
    $resultDelivered = $conn->query($sqlDelivered);
    $totalDelivered = ($resultDelivered->num_rows > 0) ? $resultDelivered->fetch_assoc()['total_delivered'] : 0;

    $sqlRejected = "SELECT COUNT(*) AS total_rejected FROM reportes_voice WHERE evento = 'rejected'";
    $resultRejected = $conn->query($sqlRejected);
    $totalRejected = ($resultRejected->num_rows > 0) ? $resultRejected->fetch_assoc()['total_rejected'] : 0;

    return array($totalDelivered, $totalRejected);
}

?>
