<?php
session_start();
include('partials/dbconnection.php');

if (isset($_POST["confirm_otp"])) {
    $entered_otp = $_POST["otp"];
    $email = $_SESSION['email']; // Retrieve email from session

    // Get the OTP from the database (or session)
    $result = mysqli_query($con, "SELECT otp FROM tbluser WHERE email = '$email'");
    $row = mysqli_fetch_assoc($result);
    $stored_otp = $row['otp'];

    // Check if the entered OTP matches the one in the database
    if ($entered_otp == $stored_otp) {
        // Mark the user as verified
        $update = mysqli_query($con, "UPDATE tbluser SET is_verified = 1 WHERE email = '$email'");

        if ($update) {
            echo "<script>alert('OTP Verified Successfully. Your account is now active.');</script>";
            echo "<script>window.location.href='login.php';</script>"; // Redirect to login
        } else {
            echo "<script>alert('Verification failed. Please try again later.');</script>";
        }
    } else {
        echo "<script>alert('Invalid OTP. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>OTP Confirmation</title>
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <link rel="shortcut icon" href="images/favicon.png" />
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo">
                <img src="images/minnel-logo.svg" alt="logo">
              </div>
              <h4>Confirm OTP</h4>
              <h6 class="font-weight-light">Enter the OTP sent to your email</h6>
              <form class="pt-3" method="POST" action="">
                <div class="form-group">
                  <input type="text" class="form-control form-control-lg" name="otp" placeholder="Enter OTP" required>
                </div>
                <div class="mt-3">
                  <input type="submit" name="confirm_otp" class="btn btn-block btn-success btn-lg font-weight-medium auth-form-btn" value="Confirm">
                </div>
              </form>

              <div class="text-center mt-4 font-weight-light">
                Didn't receive an OTP? <a href="register.php" class="text-primary">Resend</a>
              </div>
              <div class="text-center mt-4 font-weight-light">
                Already confirmed? <a href="login.php" class="text-primary">Login</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="vendors/js/vendor.bundle.base.js"></script>
</body>
</html>
