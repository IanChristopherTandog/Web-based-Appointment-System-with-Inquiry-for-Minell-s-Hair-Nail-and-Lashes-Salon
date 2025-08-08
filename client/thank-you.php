<?php
session_start();
include('includes/dbconnection.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader (assuming you're using Composer)
require '../admin/vendor/autoload.php';

function checkIfLoggedIn() {
  if (!isset($_SESSION['email'])) {
      // If the user is not logged in, redirect to login.php
      header('Location: ../admin/login.php');
      exit();
  }
}

checkIfLoggedIn();

// Check if the user is logged in and their email is stored in the session
if (isset($_SESSION['email'])) {
  $userEmail = $_SESSION['email']; // Retrieve the user's email from the session

  // Fetch the user's name from the database
  $query = mysqli_query($con, "SELECT Name FROM tbluser WHERE Email='$userEmail'");
  $row = mysqli_fetch_array($query);
  $userName = $row['Name'];

  // Store the user's name in the session
  $_SESSION['Name'] = $userName;

  // Fetch the most recent appointment number for the user
  $aptQuery = mysqli_query($con, "SELECT AptNumber FROM tblappointment WHERE Email='$userEmail' ORDER BY ApplyDate DESC LIMIT 1");
  $aptRow = mysqli_fetch_array($aptQuery);
  $aptno = $aptRow['AptNumber'];

  // Store the appointment number in the session
  $_SESSION['aptno'] = $aptno;

} else {
  // If no user is logged in, redirect to the login page or show an error
  die('User not logged in.');
}


$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();                                            // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'kawamatsumachi@gmail.com';               // SMTP username
    $mail->Password   = 'hnlnepjapbvbsadw';                     // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
    $mail->Port       = 587;                                    // TCP port to connect to

    // Recipients
    $mail->setFrom('kawamatsumachi@gmail.com', 'Minnel\'s Salon');
    $mail->addAddress($userEmail);  // Send the email to the logged-in user's email

    // Content
    $mail->isHTML(true);                                        // Set email format to HTML
    $mail->Subject = 'Thank You for Applying';
    $mail->Body    = '
        <div style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 40px; color: #333;">
            <div style="max-width: 600px; margin: auto; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                <div style="text-align: center; margin-bottom: 30px;">
                    <h2 style="color: #FFDF00; margin-bottom: 0;">Minnel\'s Salon</h2>
                    <p style="color: #666; font-size: 14px;">Beauty & Style at Your Fingertips</p>
                </div>
                <p style="font-size: 16px; line-height: 1.6; color: #555;">
                    Hello, Customer
                </p>
                <p style="font-size: 16px; line-height: 1.6; color: #555;">
                    Thank you for choosing Minnel\'s Salon. We are thrilled to confirm your appointment.
                </p>
                <div style="font-size: 18px; line-height: 1.6; color: #333; background-color: #f0f0f0; padding: 15px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
                    <strong>Your Appointment Number:</strong> <span style="color: #000000;">' . $_SESSION['aptno'] . '</span>
                </div>
                <p style="font-size: 16px; line-height: 1.6; color: #555;">
                    We look forward to providing you with the best service experience. If you have any questions or need to reschedule, please don\'t hesitate to contact us.
                </p>
                <div style="text-align: center; margin-top: 30px;">
                    <a href="http://minellssalon.site/" style="background-color: #FFDF00; color: #333; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;">CLICK HERE</a>
                </div>
                <p style="font-size: 14px; color: #999; text-align: center; margin-top: 30px;">
                    &copy; ' . date("Y") . ' Minnel\'s Salon. All rights reserved.
                </p>
            </div>
        </div>';
    $mail->AltBody = 'Thank you for applying. Your Appointment no is ' . $_SESSION['aptno']; // For plain text emails

    // Send the email
    $mail->send();
    echo "<script>alert('Email has been sent');</script>";  // Alert box for successful email sending
} catch (Exception $e) {
    echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";  // Alert box for error
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Minell's Salon</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/minell-logo-nobg.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center fixed-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="homepage.php" class="logo d-flex align-items-center me-auto">
        <!-- <img src="assets/img/minell-logo-nobg.png" alt="Minnel's Salon"> -->
        <h1 class="sitename">Minell's Salon</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="homepage.php" class="active">HOME</a></li>
          <li class="dropdown"><a href="#"><?php echo htmlspecialchars(strtoupper($_SESSION['Name'])); ?></span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="view-profile.php">VIEW PROFILE</a></li>
              <li><a href="client-inbox.php">MESSAGES</a></li>
              <li><a href="logout.php">LOGOUT</a></li>
            </ul>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <main class="main">

    <!-- Page Title -->
    <div class="page-title accent-background">
      <div class="container position-relative">
        <h1>MAKE AN APPOINTMENT</h1>
        <p>Transform Your Look, One Appointment at a Time! Book Now for the Style You Deserve, with Convenient Scheduling and Personalized Care.</p>
        <nav class="breadcrumbs">
          <ol>
            <li><a href="homepage.php">Home</a></li>
            <li class="current">Make an appointment</li>
          </ol>
        </nav>
      </div>
    </div><!-- End Page Title -->

    <!-- Starter Section Section -->
    <section id="starter-section" class="starter-section section">

<!-- Section Title -->
<div class="container section-title" data-aos="fade-up">
  <h2>Thank You</h2>
  <p>Thank you for applying. Your Appointment no is <?php echo $_SESSION['aptno'];?></p>
  <p>You can check your email or your profile for more details of your appointment.</p>
</div><!-- End Section Title -->

</section>


  </main>

  <footer id="footer" class="footer accent-background">

  <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-5 col-md-12 footer-about">
          <a href="index.php" class="logo d-flex align-items-center">
            <span class="sitename">Minell's Salon</span>
          </a>
          <p>We pride ourselves on our high quality work and attention to detail. The products we use are of top quality branded products.</p>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-6 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">About us</a></li>
            <li><a href="#">Services</a></li>
            <!-- <li><a href="#">Terms of service</a></li>
            <li><a href="#">Privacy policy</a></li> -->
          </ul>
        </div>

        <div class="col-lg-2 col-6 footer-links">
          <h4>Our Services</h4>
          <!-- <ul>
            <li><a href="#">Web Design</a></li>
            <li><a href="#">Web Development</a></li>
            <li><a href="#">Product Management</a></li>
            <li><a href="#">Marketing</a></li>
            <li><a href="#">Graphic Design</a></li>
          </ul> -->
        </div>

        <div class="col-lg-3 col-md-12 footer-contact text-center text-md-start">
          <h4>Contact Us</h4>
          <p>Langkaan,</p>
          <p>Dasmarinas City, Cavite</p>
          <p class="mt-4"><strong>Phone:</strong> <span>+639683622371</span></p>
          <p><strong>Email:</strong> <span>minells.salon@gmail.com</span></p>
        </div>

      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
      window.embeddedChatbotConfig = {
      chatbotId: "sd6FoUlgpRjbJQJDuNyK0",
      domain: "www.chatbase.co"
      }
    </script>

    <script
      src="https://www.chatbase.co/embed.min.js"
      chatbotId="sd6FoUlgpRjbJQJDuNyK0"
      domain="www.chatbase.co"
      defer>
    </script>
    
</body>

</html>