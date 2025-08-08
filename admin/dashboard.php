    <?php
    session_start();
    error_reporting(0);
    include('partials/dbconnection.php');
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/autoload.php';

    // Redirect if not logged in
    if (strlen($_SESSION['salondbaid']) == 0) {
        header('location:login.php');
    }

    // Get today's date in the format your database uses
    $today = date('Y-m-d');

    // Query to count appointments for today
    $appointmentQuery = mysqli_query($con, "SELECT COUNT(*) as count FROM tblappointment WHERE DATE(AptDate) = '$today'");
    $row = mysqli_fetch_assoc($appointmentQuery);
    $todayAppointments = $row['count'];

    // Query to get Appointments today
    $queryTodayAppointments = mysqli_query($con, "SELECT * FROM tblappointment WHERE AptDate = '$today'");
    if ($queryTodayAppointments) {
        $todayAppointmentsData = mysqli_fetch_all($queryTodayAppointments, MYSQLI_ASSOC);
    } else {
        // Log the error or display a message
        echo "Error fetching today's appointments: " . mysqli_error($con);
        $todayAppointmentsData = []; // Set to an empty array to avoid the warning
    }


    // Query for total appointments this week
    $queryWeek = mysqli_query($con, "SELECT COUNT(*) as weeklyAppointments FROM tblappointment WHERE WEEK(AptDate) = WEEK(CURDATE())");
    $rowWeek = mysqli_fetch_assoc($queryWeek);
    $weeklyAppointments = $rowWeek['weeklyAppointments'];

    // Query for total appointments this month
    $queryMonth = mysqli_query($con, "SELECT COUNT(*) as monthlyAppointments FROM tblappointment WHERE MONTH(AptDate) = MONTH(CURDATE())");
    $rowMonth = mysqli_fetch_assoc($queryMonth);
    $monthlyAppointments = $rowMonth['monthlyAppointments'];

    // Query for total appointments last month
    $queryLastMonth = mysqli_query($con, "SELECT COUNT(*) as lastMonthAppointments FROM tblappointment WHERE MONTH(AptDate) = MONTH(CURDATE() - INTERVAL 1 MONTH)");
    $rowLastMonth = mysqli_fetch_assoc($queryLastMonth);
    $lastMonthAppointments = $rowLastMonth['lastMonthAppointments'];

    if ($lastMonthAppointments > 0) {
        // Percentage change formula
        $percentageChange = (($monthlyAppointments - $lastMonthAppointments) / $lastMonthAppointments) * 100;
        $percentageChange = round($percentageChange, 2); // Rounding to two decimal places
    } else {
        $percentageChange = $monthlyAppointments > 0 ? 100 : 0; // Avoid division by zero, handle cases with no appointments last month
    }

    //Count users
    $query1 = mysqli_query($con, "SELECT * FROM tbluser");
    $totalcust = mysqli_num_rows($query1);

    // Get current month and last month counts
    $currentMonth = date('Y-m');
    $lastMonth = date('Y-m', strtotime('-1 month'));

    // Query for current month
    $queryCurrent = mysqli_query($con, "SELECT COUNT(*) AS total FROM tbluser WHERE DATE_FORMAT(reg_date, '%Y-%m') = '$currentMonth'");
    $currentMonthCount = mysqli_fetch_assoc($queryCurrent)['total'];

    // Query for last month
    $queryLast = mysqli_query($con, "SELECT COUNT(*) AS total FROM tbluser WHERE DATE_FORMAT(reg_date, '%Y-%m') = '$lastMonth'");
    $lastMonthCount = mysqli_fetch_assoc($queryLast)['total'];

    // Calculate percentage change
    if ($lastMonthCount > 0) {
        $percentageChange = (($currentMonthCount - $lastMonthCount) / $lastMonthCount) * 100;
    } else {
        $percentageChange = $currentMonthCount > 0 ? 100 : 0; // If last month count is 0 and current month count is more than 0, it's a 100% increase.
    }

    //Count total sum of Inquiries from both tblreplies and tblinquiries and display the percentage of comparison of inquiries from this month to last month
    // Count inquiries
    $result_this_month = mysqli_query($con, "SELECT COUNT(*) AS total_inquiries_this_month FROM tblinquiry WHERE MONTH(submit_date) = MONTH(CURDATE()) AND YEAR(submit_date) = YEAR(CURDATE())");
    $result_last_month = mysqli_query($con, "SELECT COUNT(*) AS total_inquiries_last_month FROM tblinquiry WHERE MONTH(submit_date) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR(submit_date) = YEAR(CURDATE())");

    $this_month_data = mysqli_fetch_assoc($result_this_month);
    $last_month_data = mysqli_fetch_assoc($result_last_month);

    $total_inquiries_this_month = $this_month_data['total_inquiries_this_month'];
    $total_inquiries_last_month = $last_month_data['total_inquiries_last_month'];

    // Calculate percentage change for inquiries
    if ($total_inquiries_last_month > 0) {
        $percentage_change = (($total_inquiries_this_month - $total_inquiries_last_month) / $total_inquiries_last_month) * 100;
        $percentage_change = round($percentage_change, 2);
    } else {
        $percentage_change = $total_inquiries_this_month > 0 ? 100 : 0;
    }
    $totalInquiriesQuery = mysqli_query($con, "
        SELECT 
            (SELECT COUNT(*) FROM tblinquiry) AS inquiry_count,
            (SELECT COUNT(*) FROM tblreplies WHERE reply_by != 'ADMIN') AS reply_count
    ");

    // Fetch the results
    $totalInquiriesData = mysqli_fetch_assoc(result: $totalInquiriesQuery);

    // Sum the counts
    $totalInquiriesAllTime = $totalInquiriesData['inquiry_count'] + $totalInquiriesData['reply_count'];

    // Count Appointments. // Get total appointments for this month
    $currentMonth = date('Y-m'); // Current month in YYYY-MM format
    $queryCurrentMonth = mysqli_query($con, "SELECT COUNT(*) as currentMonthAppointments FROM tblappointment WHERE AptDate LIKE '$currentMonth%'");
    $rowCurrentMonth = mysqli_fetch_assoc($queryCurrentMonth);
    $currentMonthAppointments = $rowCurrentMonth['currentMonthAppointments'];

    // Get total appointments for last month
    $lastMonth = date('Y-m', strtotime('-1 month')); // Last month in YYYY-MM format
    $queryLastMonth = mysqli_query($con, "SELECT COUNT(*) as lastMonthAppointments FROM tblappointment WHERE AptDate LIKE '$lastMonth%'");
    $rowLastMonth = mysqli_fetch_assoc($queryLastMonth);
    $lastMonthAppointments = $rowLastMonth['lastMonthAppointments'];

    // Calculate percentage change
    if ($lastMonthAppointments > 0) {
        $percentageChange = (($currentMonthAppointments - $lastMonthAppointments) / $lastMonthAppointments) * 100;
    } else {
        $percentageChange = $currentMonthAppointments > 0 ? 100 : 0; // If there are appointments this month but none last month
    }

    // Determine the display message
    if ($percentageChange > 0) {
        $displayMessage = sprintf("%.2f%% more appointments than last month", $percentageChange);
    } elseif ($percentageChange < 0) {
        $displayMessage = sprintf("%.2f%% fewer appointments than last month", abs($percentageChange));
    } else {
        $displayMessage = "Same number of appointments as last month";
    }

    // Total appointments query
    $queryTotalAppointments = mysqli_query($con, "SELECT COUNT(*) as totalAppointments FROM tblappointment");
    $rowTotalAppointments = mysqli_fetch_assoc($queryTotalAppointments);
    $totalappointment = $rowTotalAppointments['totalAppointments'];

    // Sales..
    // Get current month and last month for sales calculations
    $currentMonth = date('Y-m'); // Current month in YYYY-MM format
    $lastMonth = date('Y-m', strtotime('-1 month')); // Last month in YYYY-MM format

    // Query for total sales this month
    $queryCurrentMonthSales = mysqli_query($con, "SELECT SUM(Price) AS totalSales FROM tblappointment WHERE AptDate LIKE '$currentMonth%' AND Status = 3");
    $currentMonthSalesData = mysqli_fetch_assoc($queryCurrentMonthSales);
    $currentMonthSales = $currentMonthSalesData['totalSales'] ?? 0; // Default to 0 if null

    // Query for total sales last month
    $queryLastMonthSales = mysqli_query($con, "SELECT SUM(Price) AS totalSales FROM tblappointment WHERE AptDate LIKE '$lastMonth%' AND Status = 3");
    $lastMonthSalesData = mysqli_fetch_assoc($queryLastMonthSales);
    $lastMonthSales = $lastMonthSalesData['totalSales'] ?? 0; // Default to 0 if null


    // Query for total sales of all time
    $queryTotalSales = mysqli_query($con, "SELECT SUM(Price) AS totalSales FROM tblappointment WHERE Status = 3");
    $totalSalesData = mysqli_fetch_assoc($queryTotalSales);
    $totalSales = $totalSalesData['totalSales'] ?? 0; // Default to 0 if null

    // Calculate percentage change
    if ($lastMonthSales > 0) {
        $percentageChange = (($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100;
        $percentageChange = round($percentageChange, 2); // Rounding to two decimal places
        $inquiryMessage = $percentageChange > 0 ? sprintf("%.2f%% more than last month", $percentageChange) : sprintf("%.2f%% fewer than last month", abs($percentageChange));
    } else {
        $percentageChange = $currentMonthSales > 0 ? 100 : 0; // If last month count is 0 and current month count is more than 0, it's a 100% increase.
        $inquiryMessage = $currentMonthSales > 0 ? "100% more than last month" : "No sales this month.";
    }
    function sendEmail($to, $name, $services, $time) {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'kawamatsumachi@gmail.com'; // Your SMTP username
            $mail->Password = 'hnlnepjapbvbsadw'; // Your SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587; // TCP port to connect to

            // Recipients
            $mail->setFrom('kawamatsumachi@gmail.com', 'Minnel\'s Salon');
            $mail->addAddress($to); // Add a recipient

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Appointment Reminder';
            $mail->Body = '
            <div style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 40px; color: #333;">
                <div style="max-width: 600px; margin: auto; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <h2 style="color: #FFDF00; margin-bottom: 0;">Minnel\'s Salon</h2>
                        <p style="color: #666; font-size: 14px;">Beauty & Style at Your Fingertips</p>
                    </div>
                    <div style="padding: 20px; background: white; border-radius: 0 0 8px 8px; line-height: 1.6;">
                        <p style="margin: 0;">Dear <strong>' . $name . '</strong>,</p>
                        <p>You have an appointment today for the following service:</p>
                        <div style="padding: 10px; background-color: #f8f9fa; border-radius: 5px;">
                            <p style="margin: 0;"><strong>Service:</strong> ' . $services . '</p>
                            <p style="margin: 0;"><strong>Time:</strong> ' . $time . '</p>
                        </div>
                        <p style="margin: 0;">Thank you for choosing our salon!</p>
                    </div>
                    <div style="text-align: center; padding: 10px; font-size: 0.9em; color: #777; background-color: #f4f4f4; border-radius: 0 0 8px 8px;">
                        &copy; ' . date("Y") . ' Minnel\'s Salon. All rights reserved.
                    </div>
                </div>
            </div>
            ';

            // Send the email
            $mail->send();
            echo '<script>alert("Email sent successfully!");</script>';
            return true;
        } catch (Exception $e) {
            echo '<script>alert("Email could not be sent. Please try again later.");</script>';
            return false;
        }
    }


    // Handle the email sending process
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_email'])) {
        foreach ($todayAppointmentsData as $appointment) {
            $email = $appointment['Email']; // Assuming you have an 'Email' column in tblappointment
            $name = $appointment['Name'];
            $services = $appointment['Services'];
            $time = $appointment['AptTime'];

            sendEmail($email, $name, $services, $time);
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
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" type="text/css" href="js/select.dataTables.min.css">
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/favicon.png" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


    <style>
    body {
        background-color: #fffde6; /* Light blue background for the page */
        color: #333; /* Darker font color for better readability */
    }

    #appointmentCalendar {
        max-width: 800px; /* Set a maximum width */
        max-height: 400px; /* Set a maximum height */
        border-radius: 8px; /* Rounded corners */
        padding: 10px; /* Add some padding inside the calendar */
    }

    .fc {
        font-size: 0.8em; /* Adjust font size for the FullCalendar */
    }

    .fc-event {
        border-radius: 5px; /* Rounded corners for events */
        color: black; /* White text for event titles for better contrast */
    }

    .fc-daygrid-event {
        padding: 4px; /* Adjust padding inside the event blocks */
    }

    /* Change the header icon color to black */
    .fc-toolbar .fc-button {
        color: black; /* Change the icon color to black */
    }

    .fc-toolbar .fc-button .fc-icon {
        color: black; /* Ensure icons are black */
    }
</style>


</head>

<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.php -->
        <?php include_once('partials/_navbar.php'); ?>
        
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.php -->
            <?php include_once('partials/_sidebar.php'); ?>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <!-- <div class="row">
                        <div class="col-md-12 grid-margin">
                            <div class="row">
                            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                                    <h3 class="font-weight-bold">Welcome Admin</h3>
                                    <h6 class="font-weight-normal mb-0">All systems are running smoothly! You have <span class="text-primary"><?php echo $todayAppointments; ?> appointments today!</span></h6>
                                </div>

                            </div>
                        </div>
                    </div> -->
                    <div class="row">
                        <div class="col-md-3 mb-4 stretch-card transparent">
                            <div class="card card-dark-blue">
                                <div class="card-body">
                                    <a href="all-appointments.php" style="text-decoration: none; color: inherit;">
                                        <p><h4 class="mb-0"><i class="bi bi-calendar-check">    </i>Total Appointments:  <?php echo $totalappointment; ?></h4> <br></p>
                                        <p><?php echo $displayMessage; ?></p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4 stretch-card transparent">
                            <div class="card card-tale">
                                <div class="card-body">
                                    <a href="inquiries.php" style="text-decoration: none; color: inherit;">
                                        <p>
                                            <h4 class="mb-0"><i class="bi bi-chat-dots"></i>    Total Inquiries: <?= $totalInquiriesAllTime ?></h4><br>
                                        </p>
                                        <p>
                                            <?= $percentage_change > 0 ? $percentage_change . "% more than last month" : ($percentage_change < 0 ? abs($percentage_change) . "% fewer than last month" : "Same as last month") ?>
                                        </p>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-4 stretch-card transparent">
                            <div class="card card-light-danger">
                                <div class="card-body">
                                    <a href="clients.php" style="text-decoration: none; color: inherit;">
                                        <p><h4 class="mb-0"><i class="bi bi-people">    </i>Registered Clients:  <?php echo $totalcust; ?></h4> <br></p>
                                        <p>
                                            <?php
                                            if ($currentMonthCount == $lastMonthCount) {
                                                echo 'No change compared to last month.';
                                            } else {
                                                echo round(abs($percentageChange), 2) . '% ' . ($percentageChange > 0 ? 'more' : 'less') . ' than last month';
                                            }
                                            ?>
                                        </p>
                                    </a>
                                </div>
                            </div>
                        </div>  
                        <div class="col-md-3 mb-4 stretch-card transparent">
                            <div class="card card-light-blue">
                                <div class="card-body">
                                    <a href="reports.php" style="text-decoration: none; color: inherit;">
                                        <p>
                                            <h4 class="mb-0"><i class="bi bi-cash-coin">    </i>Total Sales: ₱<?php echo number_format($totalSales, 2); ?></h4>
                                        </p>
                                        <p><br>
                                        <?php echo $inquiryMessage; ?></p>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <!-- Calendar Stretch Card -->
                        <div class="col-md-8 grid-margin stretch-card">
                            <div class="card tale-bg">
                                <div class="card-body">
                                    <!-- Legend for Status Colors -->
                                        <div class="d-flex justify-content-center align-items-center mt-2">
                                            <strong class="me-3">Status Legend:</strong>
                                            <!-- Completed -->
                                            <div class="d-flex align-items-center status-item">
                                                <span class="status-circle bg-primary me-2"></span> Completed
                                            </div>
                                            <!-- Ongoing -->
                                            <div class="d-flex align-items-center status-item">
                                                <span class="status-circle bg-success me-2"></span> Ongoing
                                            </div>
                                            <!-- Upcoming -->
                                            <div class="d-flex align-items-center status-item">
                                                <span class="status-circle bg-warning me-2"></span> Upcoming
                                            </div>
                                            <!-- Cancelled -->
                                            <div class="d-flex align-items-center status-item">
                                                <span class="status-circle bg-danger me-2"></span> Cancelled
                                            </div>
                                            <!-- No-Show -->
                                            <div class="d-flex align-items-center status-item">
                                                <span class="status-circle bg-secondary me-2"></span> No-Show
                                            </div>
                                        </div>
                                    <!-- Calendar Display -->
                                    <div id="appointmentCalendar"></div>
                                </div>
                            </div>
                        </div>

                        <!-- CSS for Circular Status Indicators -->
                        <style>
                            .status-circle {
                                width: 12px;
                                height: 12px;
                                border-radius: 50%;
                                display: inline-block;
                            }
                            .status-item, .me-3{
                                margin-right: 24px; /* Add spacing between items */
                            }
                        </style>


                        <div class="col-md-4 stretch-card grid-margin">
                            <div class="card">
                                <div class="card-body" style="max-height: 400px; overflow-y: auto; position: relative;">
                                    <h5 class="card-title" style="position: sticky; top: 0; background: #fff; z-index: 100; padding: 10px; border-bottom: 1px solid #e0e0e0;">
                                        Today's Appointments: <?php echo $todayAppointments; ?>
                                    </h5>
                                    <ul class="icon-data-list">
                                        <?php
                                            // Query to fetch today's appointments
                                            $today = date('Y-m-d'); // Get today's date
                                            $query = "SELECT Name, Services, AptTime,QueueNumber, Type FROM tblappointment WHERE AptDate = '$today'ORDER BY QueueNumber ASC"; // 0 for Upcoming appointments
                                            $result = mysqli_query($con, $query);

                                            // Check if any appointments exist
                                            if (mysqli_num_rows($result) > 0) {
                                                // Loop through and display each appointment
                                                while($row = mysqli_fetch_assoc($result)) {
                                                    echo '<li>';
                                                    echo '  <div class="d-flex">';
                                                    echo '      <img src="images/default.jpg" alt="user">'; // You can customize this
                                                    echo '      <div>';
                                                    echo '          <p class="text-info mb-1">' . htmlspecialchars($row['Name']) . '</p>';
                                                    echo '          <p class="mb-0">Service: ' . htmlspecialchars($row['Services']) . '</p>';
                                                    echo '          <p class="mb-0">Queue Number: ' . htmlspecialchars($row['QueueNumber']) . '</p>';
                                                    echo '          <p class="mb-0">Queue Number: ' . htmlspecialchars($row['Type']) . '</p>';
                                                    // echo '          <small>' . date("h:i A", strtotime($row['AptTime'])) . '</small>'; // Format time to 12-hour format with AM/PM

                                                    echo '      </div>';
                                                    echo '  </div>';
                                                    echo '</li>';
                                                }
                                            } else {
                                                // No appointments for today
                                                echo '<li>No appointments for today</li>';
                                            }
                                        ?>
                                    </ul>
                                    <form method="POST">
                                        <button type="submit" name="send_email" class="btn btn-primary btn-block">Send Email Reminders</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>    
                </div>          
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.php -->
                <!-- <?php include_once('partials/_footer.php'); ?>   -->
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirm Logout</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to log out?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <a href="logout.php" class="btn btn-primary">Yes, Logout</a>
                </div>
            </div>
        </div>
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="vendors/chart.js/Chart.min.js"></script>
    <script src="vendors/datatables.net/jquery.dataTables.js"></script>
    <script src="vendors/datatables.net-bs4/dataTables.bootstrap4.js"></script>
    <script src="js/dataTables.select.min.js"></script>
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="js/dashboard.js"></script>
    <script src="js/Chart.roundedBarCharts.js"></script>
    <!-- FullCalendar Initialization -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('appointmentCalendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            // Disable the time display in the event title
            displayEventTime: false,

            events: [
                <?php
                $queryAppointments = mysqli_query($con, "SELECT AptDate, AptTime, Name, Services, Status FROM tblappointment");
                while ($row = mysqli_fetch_assoc($queryAppointments)) {
                    // Convert AptTime to 24-hour format (HH:MM:SS)
                    $aptTime24 = date("H:i:s", strtotime($row['AptTime']));

                    // Combine AptDate and the converted AptTime
                    $eventDateTime = $row['AptDate'] . 'T' . $aptTime24;

                    // Create event title with just the Name and Services
                    $eventTitle = htmlspecialchars($row['Name']) . " - " . htmlspecialchars($row['Services']); 

                    // Assign background color and border color based on Status
                    $eventBackgroundColor = '';
                    switch ($row['Status']) {
                        case '0':
                            $eventBackgroundColor = '#FFFF00'; // Yellow for status 0
                            break;
                        case '1':
                            $eventBackgroundColor = '#008000'; // Green for status 1
                            break;
                        case '2':
                            $eventBackgroundColor = '#FF0000'; // Red for status 2
                            break;
                        case '3':
                            $eventBackgroundColor = '#0000FF'; // Blue for Completed (status 3)
                            break;
                        case '4':
                            $eventBackgroundColor = '#808080'; // Gray for no-show (status 4)
                            break;
                        default:
                            $eventBackgroundColor = '#378006'; // Default color if status is unknown
                    }

                    // Output the event with backgroundColor and borderColor
                    echo "{ title: '$eventTitle', start: '$eventDateTime', backgroundColor: '$eventBackgroundColor', borderColor: '$eventBackgroundColor' },";
                }
                ?>
            ],
        });

        calendar.render();
    });
</script>


</body>
</html>
