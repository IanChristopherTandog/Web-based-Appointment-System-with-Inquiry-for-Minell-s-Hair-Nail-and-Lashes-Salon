<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING); // Hide notices and warnings
session_start();
include('includes/dbconnection.php');

// Check if the user is logged in and their email is stored in the session
if (isset($_SESSION['email']) && isset($_POST['id']) && isset($_POST['reply_text'])) {
    $userEmail = $_SESSION['email']; // Retrieve the user's email from the session
    $replyText = mysqli_real_escape_string($con, $_POST['reply_text']);
    $inquiryId = mysqli_real_escape_string($con, $_POST['id']); // Use 'id' to match the inquiry

    // Fetch the user's name from the database
    $query = mysqli_query($con, "SELECT Name FROM tbluser WHERE Email='$userEmail'");
    $row = mysqli_fetch_array($query);
    $userName = $row['Name'];

    // Check if the inquiry exists
    $checkInquiryQuery = mysqli_query($con, "SELECT * FROM tblinquiry WHERE ID='$inquiryId'");
    if (mysqli_num_rows($checkInquiryQuery) > 0) {
        // Insert the reply into the tblreplies table
        $insertReplyQuery = mysqli_query($con, "INSERT INTO tblreplies (inquiry_id, reply_text, reply_by, reply_date) VALUES ('$inquiryId', '$replyText', '$userName', NOW())");
        
        if ($insertReplyQuery) {
            header("Location: client-inbox.php"); // Redirect to client inbox after successful reply
            exit(); // Ensure no further code is executed after redirect
        } else {
            echo "Error sending reply: " . mysqli_error($con);
        }
    } else {
        echo "Error: The inquiry ID does not exist.";
    }
} else {
    echo "Error: Missing required parameters.";
}
?>
