<?php
// Define connection parameters for both live and local environments
$live_host = "localhost";
$live_user = "u910139511_root";
$live_password = "minellssal0nDB!";
$live_database = "u910139511_salondb";

$local_host = "localhost";
$local_user = "root";
$local_password = ""; // Use the password set for your local MySQL server, or leave blank if none
$local_database = "salondb"; // Adjust the database name for your local environment if different

// Check if the script is running on a local server or live server
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    // Connect using local environment credentials
    $con = mysqli_connect($local_host, $local_user, $local_password, $local_database);
} else {
    // Connect using live server credentials
    $con = mysqli_connect($live_host, $live_user, $live_password, $live_database);
}

// Check the connection
if (mysqli_connect_errno()) {
    echo "Connection failed: " . mysqli_connect_error();
    exit();
}
?>
