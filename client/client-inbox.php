<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING); // Hide notices and warnings
session_start();
include('includes/dbconnection.php');

// Check if the user is logged in and their email is stored in the session
if (isset($_SESSION['email'])) {
    $userEmail = $_SESSION['email']; // Retrieve the user's email from the session

    // Fetch the user's name from the database
    $query = mysqli_query($con, "SELECT Name FROM tbluser WHERE Email='$userEmail'");
    $row = mysqli_fetch_array($query);
    $userName = $row['Name'];

    // Store the user's name in the session
    $_SESSION['Name'] = $userName;
} else {
    header('Location: index.php'); // Redirect if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Minell's Salon - Client Inbox</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="assets/img/minell-logo-nobg.png.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

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
                <h1 class="sitename">Minell's Salon</h1>
            </a>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="homepage.php" class="active">HOME</a></li>
                    <li class="dropdown"><a href="#"><?php echo htmlspecialchars(strtoupper($_SESSION['Name'])); ?></a>
                        <ul>
                            <li><a href="view-profile.php">VIEW PROFILE</a></li>
                            <li><a href="client-inbox.php">MESSAGES</a></li>
                            <li><a href="logout.php">LOGOUT</a></li>
                        </ul>
                    </li>
                </ul>
                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

        </div>
    </header>

    <main class="main">

        <!-- Page Title -->
        <div class="page-title accent-background">
            <div class="container position-relative">
                <h1>INBOX</h1>
                <p>Send your message or reply to Minell's Salon</p>
                <nav class="breadcrumbs">
                    <ol>
                        <li><a href="homepage.php">Home</a></li>
                        <li class="current">Client Inbox</li>
                    </ol>
                </nav>
            </div>
        </div><!-- End Page Title -->

        <!-- Inbox Section -->
        <section id="inbox-section" class="inbox-section section">
            <div class="container">
                <h2>Your Inquiries</h2>
                <?php
                $inquiriesQuery = mysqli_query($con, "SELECT * FROM tblinquiry WHERE email='$userEmail' ORDER BY submit_date DESC");

                if (mysqli_num_rows($inquiriesQuery) > 0) { // Check if there are any inquiries
                    while ($inquiryRow = mysqli_fetch_assoc($inquiriesQuery)) {
                        $inquiryId = $inquiryRow['ID']; // Use the correct column name
                        ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($inquiryRow['subject']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($inquiryRow['message']); ?></p>
                                <p class="card-text"><small class="text-muted">Submitted on: <?php echo htmlspecialchars($inquiryRow['submit_date']); ?></small></p>

                                <!-- Reply Button -->
                                <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#replyModal<?php echo $inquiryId; ?>">Reply</button>
                            </div>
                        </div>

                        <!-- Display Replies -->
                        <?php
                        $repliesQuery = mysqli_query($con, "SELECT * FROM tblreplies WHERE inquiry_id = '$inquiryId' ORDER BY reply_date DESC");
                        if (mysqli_num_rows($repliesQuery) > 0) {
                            while ($replyRow = mysqli_fetch_assoc($repliesQuery)) {
                                ?>
                                <div class="card mt-2">
                                    <div class="card-body">
                                        <strong>Reply By:</strong> <?php echo htmlspecialchars($replyRow['reply_by']); ?><br>
                                        <strong>Reply:</strong> <?php echo htmlspecialchars($replyRow['reply_text']); ?><br>
                                        <small><em><?php echo htmlspecialchars($replyRow['reply_date']); ?></em></small>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="alert alert-secondary mt-2">No replies yet.</div>';
                        }
                        ?>

                        <!-- Reply Modal -->
                        <div class="modal fade" id="replyModal<?php echo $inquiryId; ?>" tabindex="-1" aria-labelledby="replyModalLabel<?php echo $inquiryId; ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="replyModalLabel<?php echo $inquiryId; ?>">Reply to Inquiry</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="send_reply.php" method="post">
                                            <input type="hidden" name="id" value="<?php echo $inquiryId; ?>"> <!-- Use ID here -->
                                            <div class="mb-3">
                                                <label for="replyText" class="form-label">Your Reply</label>
                                                <textarea class="form-control" id="replyText" name="reply_text" required></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label for="replyBy" class="form-label">Reply By</label>
                                                <input type="text" class="form-control" id="replyBy" name="reply_by" value="<?php echo htmlspecialchars($_SESSION['Name']); ?>" readonly>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Send Reply</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                } else {
                    // No inquiries found
                    echo '<div class="alert alert-info">You have no inquiries.</div>';
                }
                ?>
            </div>
        </section>

    </main>

    <footer id="footer" class="footer accent-background">
        <div class="container footer-top">
            <div class="row gy-4">
                <div class="col-lg-5 col-md-12 footer-about">
                    <a href="index.php" class="logo d-flex align-items-center">
                        <span class="sitename">Minell's Salon</span>
                    </a>
                    <p>We pride ourselves on our high-quality work and attention to detail. The products we use are of the highest quality and provide long-lasting results.</p>
                    <div class="social-links d-flex">
                        <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12 footer-links">
                    <h4>Useful Links</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Services</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-12 footer-contact">
                    <h4>Contact Us</h4>
                    <p>
                        123 Main St, Anytown<br>
                        City, State, ZIP<br>
                        <strong>Phone:</strong> +1 234 567 890<br>
                        <strong>Email:</strong> info@minellssalon.com<br>
                    </p>

                </div>
            </div>
        </div>

        <div class="container footer-bottom clearfix">
            <div class="copyright">
                &copy; <strong><span>Minell's Salon</span></strong>. All Rights Reserved
            </div>
            <div class="credits">
                Designed by <a href="#">Your Name</a>
            </div>
        </div>
    </footer>

    <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>

</body>

</html>
