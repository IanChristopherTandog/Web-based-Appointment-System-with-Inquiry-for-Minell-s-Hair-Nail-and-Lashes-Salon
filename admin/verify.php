<?php
session_start();
include('partials/dbconnection.php');

if(isset($_GET['token'])) {
    $token = $_GET['token'];

    // Find user with this token
    $stmt = $con->prepare("SELECT * FROM tbluser WHERE verification_token = ? AND is_verified = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1) {
        // Token is valid
        $stmt = $con->prepare("UPDATE tbluser SET is_verified = 1, verification_token = NULL WHERE verification_token = ?");
        $stmt->bind_param("s", $token);
        if($stmt->execute()) {
            echo "<script>alert('Your email has been verified successfully. You can now log in.'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Failed to verify your email.'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid or expired token.'); window.location.href='login.php';</script>";
    }

    $stmt->close();
}
?>
