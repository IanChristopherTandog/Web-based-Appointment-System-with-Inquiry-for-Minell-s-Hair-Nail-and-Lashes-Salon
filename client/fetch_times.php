<?php
// Database connection
include('includes/dbconnection.php');
session_start();
error_reporting(E_ALL);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if (isset($_POST['adate']) && isset($_POST['duration'])) {
    $adate = mysqli_real_escape_string($con, $_POST['adate']);
    $duration = (int)$_POST['duration']; // Duration in minutes

    // Define start and end times in minutes
    $start_time = strtotime('09:00 am');
    $end_time = strtotime('06:00 pm');

    // Fetch all booked time slots for the selected date
    $query = "SELECT AptTime FROM tblappointment WHERE AptDate=?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $adate);
    $stmt->execute();
    $result = $stmt->get_result();
    $booked_times = [];

    while ($row = $result->fetch_assoc()) {
        $booked_times[] = $row['AptTime'];
    }

    // Create an array to hold all blocked times
    $blocked_times = [];
    foreach ($booked_times as $booked_time) {
        $start_time_booked = strtotime($booked_time);
        $end_time_booked = $start_time_booked + ($duration * 60); // Calculate end time

        // Block out all times between start and end time
        for ($time = $start_time_booked; $time < $end_time_booked; $time += 30 * 60) { // Increment by 30 mins
            $blocked_times[] = date('h:i A', $time);
        }
    }

    // Generate available and unavailable times based on service duration
    $time_slots = []; // This will hold both available and unavailable times
    for ($time = $start_time; $time <= $end_time; $time += 30 * 60) { // Check each 30-minute interval
        $formatted_time = date('h:i A', $time);
        $end_time_slot = $time + ($duration * 60); // Calculate end time of this slot

        if ($end_time_slot > $end_time) {
            // If the end time of the slot exceeds closing time, skip this time
            continue;
        }

        if (in_array($formatted_time, $blocked_times)) {
            // If the time is blocked, display as disabled with an indication
            $time_slots[] = "<option value='$formatted_time' disabled>$formatted_time - " . date('h:i A', $end_time_slot) . " (Unavailable)</option>";
        } else {
            // If the time is available, display as an option
            $time_slots[] = "<option value='$formatted_time'>$formatted_time - " . date('h:i A', $end_time_slot) . "</option>";
        }
    }

    // Output the time slots
    if (empty($time_slots)) {
        echo "<option value='' disabled>No available times</option>";
    } else {
        foreach ($time_slots as $slot) {
            echo $slot;
        }
    }

    $stmt->close();
} else {
    echo "<option value='' disabled>Please select a date and service first</option>";
}

$con->close();
?>
