<?php
// Include the database connection file
include 'dbconnection.php'; // Make sure this path is correct

// Start output buffering to capture alerts
ob_start();

// Check if the user is logged in by checking the session variable
session_start();
$redirect_page = isset($_SESSION['email']) ? '../homepage.php' : '../index.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ensure $conn is available before using it
    if (isset($con)) {
        $name = $con->real_escape_string($_POST['name']);
        $email = $con->real_escape_string($_POST['email']);
        $subject = $con->real_escape_string($_POST['subject']);
        $message = $con->real_escape_string($_POST['message']);
        $submit_date = date('Y-m-d H:i:s'); // Current date and time

        // Insert inquiry for REGISTERED user
        $sql = "INSERT INTO tblinquiry (name, email, subject, message, submit_date, user_type) 
        VALUES ('$name', '$email', '$subject', '$message', '$submit_date', 'REGISTERED')";


        if ($con->query($sql) === TRUE) {
            // Successful submission
            echo "<script>
                    alert('Inquiry submitted successfully!');
                    window.location.href='$redirect_page'; // Redirect based on login status
                  </script>";
        } else {
            // Error in submission
            echo "<script>
                    alert('Error: " . $con->error . "');
                    window.location.href='$redirect_page'; // Redirect based on login status
                  </script>";
        }
    } else {
        echo "<script>
                alert('Database connection is not established.');
                window.location.href='$redirect_page'; // Redirect based on login status
              </script>";
    }
}

// Close the database connection
$con->close();

// Flush the output buffer
ob_end_flush();
?>
