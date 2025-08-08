<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if (isset($_POST["register"])) {
  $name = $_POST["name"];
  $email = $_POST["email"];
  $password = $_POST["password"];
  $confirmpassword = $_POST["confirm_password"];
  $reg_date = date("Y-m-d H:i:s"); // Get the current date and time

  // Validate the name (only letters and spaces are allowed)
  if (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
      $msg = "Name can only contain letters and spaces.";
  } else {
      // Check for duplicate email
      $duplicate = mysqli_query($con, "SELECT * FROM tbluser WHERE email = '$email'");
      if (mysqli_num_rows($duplicate) > 0) {
          $msg = "Email has already been taken.";
      } else {
          if ($password == $confirmpassword) {
              // Hash the password before storing it
              $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

              // Generate a random 6-digit OTP
              $otp = rand(100000, 999999);

              // Store OTP in session temporarily (or store it in the database)
              $_SESSION['otp'] = $otp;
              $_SESSION['email'] = $email;

              // Prepared statement to prevent SQL injection
              $stmt = $con->prepare("INSERT INTO tbluser (name, email, password, otp, is_verified, reg_date) VALUES (?, ?, ?, ?, 0, ?)");
              $stmt->bind_param("sssss", $name, $email, $hashedPassword, $otp, $reg_date);

              if ($stmt->execute()) {
                  // Send OTP email
                  $mail = new PHPMailer(true);
                  try {
                      // Server settings
                      $mail->isSMTP();
                      $mail->Host = 'smtp.gmail.com';
                      $mail->SMTPAuth = true;
                      $mail->Username = 'kawamatsumachi@gmail.com'; // SMTP username
                      $mail->Password = 'hnlnepjapbvbsadw'; // SMTP password
                      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                      $mail->Port = 587;

                      // Recipients
                      $mail->setFrom('kawamatsumachi@gmail.com', 'Minnel\'s Salon');
                      $mail->addAddress($email);

                      $mail->isHTML(true);
                      $mail->Subject = 'Your OTP Code for Registration';
                      $mail->Body = "
                      <div style='font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 40px; color: #333;'>
                          <div style='max-width: 600px; margin: auto; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);'>
                              <div style='text-align: center; margin-bottom: 30px;'>
                                  <h2 style='color: #FFDF00; margin-bottom: 0;'>Minnel's Salon</h2>
                                  <p style='color: #666; font-size: 14px;'>Beauty & Style at Your Fingertips</p>
                              </div>
                              <div style='padding: 30px; text-align: center;'>
                                  <div style='font-size: 16px; color: #333; margin-bottom: 20px;'>
                                      Thank you for registering with us! We received your registration request and just need a quick verification.
                                  </div>
                                  <div style='font-size: 24px; font-weight: bold; color: #000000; margin-bottom: 20px;'>
                                      <b>$otp</b>
                                  </div>
                                  <div style='font-size: 14px; color: #666;'>
                                      Please enter this code on the website to complete your registration. If you did not request registration, you can safely ignore this email.
                                  </div>
                              </div>
                              <div style='padding: 20px; background-color: #f0f0f0; text-align: center; font-size: 12px; color: #999;'>
                                  This email was sent by <b>Minell's Salon</b>. If you did not make this request, please contact support.
                              </div>
                          </div>
                      </div>
                      ";

                      $mail->send();

                      // Redirect to OTP confirmation page
                      header("Location: confirm_otp.php");
                      exit();
                  } catch (Exception $e) {
                      echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
                  }
              } else {
                  echo "<script>alert('Registration Failed');</script>";
              }
              $stmt->close();
          } else {
              echo "<script>alert('Passwords do not match');</script>";
          }
      }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Minell's Admin</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="vendors/feather/feather.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
  <!-- inject:css -->
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">
  <link rel="shortcut icon" href="images/favicon.png" />
  <script>
    function validatePassword() {
      var password = document.querySelector('input[name="password"]').value;
      var message = document.getElementById('passwordMessage');

      if (password.length < 6) {
        message.textContent = 'Password must be at least 6 characters long.';
        message.style.color = 'red';
      } else {
        message.textContent = '';
      }
    }
  </script>
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
              <h4>New here?</h4>
              <h6 class="font-weight-light">Signing up is easy. It only takes a few steps</h6>
              <form class="pt-3" method="POST" action="">
                <p style="font-size:16px; color:red" align="center"><?php echo $msg; ?></p>
                <div class="form-group">
                  <input type="text" class="form-control form-control-lg" name="name" placeholder="Name" required>
                </div>
                <div class="form-group">
                  <input type="email" class="form-control form-control-lg" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                  <input type="password" class="form-control form-control-lg" name="password" placeholder="Password" required oninput="validatePassword()">
                  <div id="passwordMessage"></div>
                </div>
                <div class="form-group">
                  <input type="password" class="form-control form-control-lg" name="confirm_password" placeholder="Confirm Password" required>
                </div>
                <div class="mb-4">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input 
                        type="checkbox" 
                        class="form-check-input" 
                        id="termsCheckbox" 
                        onclick="handleCheckboxClick(event)" 
                        required
                      >
                      I agree to all Terms & Conditions
                      
                    </label>
                  </div>
                </div>

                <!-- Terms and Conditions Modal -->
                <div 
                  class="modal fade" 
                  id="termsModal" 
                  tabindex="-1" 
                  role="dialog" 
                  aria-labelledby="termsModalLabel" 
                  aria-hidden="true"
                >
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                        <button 
                          type="button" 
                          class="close" 
                          data-dismiss="modal" 
                          aria-label="Close"
                        >
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <p>
                          By signing up, you agree to our <strong>Terms and Conditions</strong>, which outline your responsibilities as a user and our commitment to protecting your information. Key points include:
                        </p>
                        <ul>
                          <li>Providing accurate and up-to-date registration information at all times.</li>
                          <li>Maintaining the confidentiality of your account credentials and ensuring your account's security.</li>
                          <li>Adhering to our policies regarding cancellations, rescheduling, and appointment bookings.</li>
                          <li>
                            Allowing us to collect, store, and process your personal information in accordance with our 
                            <a href="#" id="privacyPolicyLink">Privacy Policy</a>, which includes details on:
                            <ul>
                              <li>What personal data we collect and why (e.g., contact details, booking history).</li>
                              <li>How we use your data to improve our services and communication.</li>
                              <li>Your rights regarding access, modification, or deletion of your data.</li>
                              <li>How we secure your information against unauthorized access.</li>
                            </ul>
                          </li>
                        </ul>
                        <p>
                          You also agree to abide by our <a href="#" id="termsOfUseLink">Terms of Use</a>, which set the rules for:
                        </p>
                        <ul>
                          <li>Appropriate use of our platform, including refraining from abusive or fraudulent activity.</li>
                          <li>Dispute resolution mechanisms in case of disagreements.</li>
                          <li>Limitations of liability regarding our services.</li>
                          <li>Our right to update these terms and notify users of significant changes.</li>
                        </ul>
                        <p>
                          Please review our <a href="#" id="privacyPolicyLinkBottom">Privacy Policy</a> and <a href="#" id="termsOfUseLinkBottom">Terms of Use</a> carefully before proceeding.
                        </p>
                      </div>
                      <div class="modal-footer">
                        <button 
                          type="button" 
                          class="btn btn-secondary" 
                          data-dismiss="modal"
                        >
                          Close
                        </button>
                        <button 
                          type="button" 
                          class="btn btn-primary" 
                          id="agreeTermsBtn" 
                          onclick="agreeToTerms()"
                        >
                          Agree
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Script for Modal Behavior -->
                <script>
                  function handleCheckboxClick(event) {
                    // Prevent direct checkbox toggle
                    event.preventDefault();
                    showTermsModal();
                  }

                  function showTermsModal(event) {
                    if (event) event.preventDefault(); // Prevent default action if triggered by link
                    $('#termsModal').modal('show'); // Show the modal
                  }

                  function agreeToTerms() {
                    $('#termsModal').modal('hide'); // Close the modal
                    document.getElementById('termsCheckbox').checked = true; // Check the checkbox
                  }
                </script>

                <div class="mt-3">
                  <input type="submit" name="register" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn" value="SIGN UP">
                </div>
                <div class="text-center mt-4 font-weight-light">
                  Already have an account? <a href="login.php" class="text-primary">Login</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="vendors/js/vendor.bundle.base.js"></script>
  <!-- inject:js -->
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/todolist.js"></script>
</body>

</html>
