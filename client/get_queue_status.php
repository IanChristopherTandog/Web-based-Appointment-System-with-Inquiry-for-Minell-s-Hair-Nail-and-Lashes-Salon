<?php
include('includes/dbconnection.php');

// Get email from the request (e.g., from session or POST data)
$email = $_POST['email'];

// Function to fetch the queue details for today
function getQueueStatus($email, $con) {
    // Get today's date
    $today = date('Y-m-d');  // Format: YYYY-MM-DD

    // Modify the query to include a check for today's appointments
    $query = "SELECT * FROM tblappointment WHERE Email='$email' AND Status IN (0, 1)";
    $ret = mysqli_query($con, $query);
    
    if (mysqli_num_rows($ret) > 0) {
        $row = mysqli_fetch_array($ret);
        $queueNumber = $row['QueueNumber'];
        $status = $row['Status'];
        $aptDate = $row['AptDate'];  // Get the appointment date
        $services = $row['Services'];  // Get the services
        $stylistName = $row['Stylist'];  // Get the stylist name from the 'Stylist' column
        
        // Count how many people are ahead in the queue
        $countQuery = "SELECT COUNT(*) AS peopleAhead FROM tblappointment WHERE QueueNumber < '$queueNumber' AND Status IN (0, 1) AND AptDate = '$today'";
        $countResult = mysqli_query($con, $countQuery);
        $countRow = mysqli_fetch_array($countResult);
        $peopleAhead = $countRow['peopleAhead'];

        // Return the details including the new fields
        return [
            'queueNumber' => $queueNumber,
            'status' => $status,
            'peopleAhead' => $peopleAhead,
            'aptDate' => $aptDate,
            'services' => $services,
            'stylistName' => $stylistName
        ];
    } else {
        return null;
    }
}

// Fetch queue details
$queueDetails = getQueueStatus($email, $con);

// Check if peopleAhead is 0 and modify the response
if ($queueDetails) {
    if ($queueDetails['peopleAhead'] == 0) {
        $queueDetails['message'] = "You're next in the queue!";
    }
} else {
    $queueDetails = ['message' => 'No appointments found for today.'];
}

// Return the result as JSON
echo json_encode($queueDetails);
?>
