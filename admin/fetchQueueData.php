<?php
session_start();
include('partials/dbconnection.php');

if (!isset($_SESSION['salondbaid'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Fetch queue data for today's appointments (Status 0)
$query = "SELECT QueueNumber, Stylist, Name, Services, Status FROM tblappointment WHERE AptDate = CURDATE() AND Status = 0 ORDER BY ApplyDate DESC";
$result = mysqli_query($con, $query);

$queueData = [];
while ($row = mysqli_fetch_assoc($result)) {
    $queueData[] = $row;
}

echo json_encode(['status' => 'success', 'data' => $queueData]);
?>
