<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');

// Redirect if not logged in
if (strlen($_SESSION['salondbaid']) == 0) {
    header('location:../index.php');
    exit();
}

// Fetch the stylist's information using the session ID
$stylistId = $_SESSION['salondbaid'];
$stylistQuery = mysqli_query($con, "SELECT * FROM tblstylist WHERE id = '$stylistId'");
$stylistData = mysqli_fetch_assoc($stylistQuery);

// Get the stylist's name (assuming 'Name' is the correct column)
$stylistName = $stylistData['name'];

// Get today's date
$today = date('Y-m-d');

// Query to count appointments for today
$appointmentQuery = mysqli_query($con, "SELECT COUNT(*) as count FROM tblappointment WHERE DATE(AptDate) = '$today' AND Stylist = '$stylistName'");
$row = mysqli_fetch_assoc($appointmentQuery);
$todayAppointments = $row['count'];

// Fetch today's appointments
$queryTodayAppointments = mysqli_query($con, "SELECT * FROM tblappointment WHERE AptDate = '$today' AND Stylist = '$stylistName'");
if ($queryTodayAppointments) {
    $todayAppointmentsData = mysqli_fetch_all($queryTodayAppointments, MYSQLI_ASSOC);
} else {
    echo "Error fetching today's appointments: " . mysqli_error($con);
    $todayAppointmentsData = []; // Avoid warnings
}

// Query for total appointments this week
$queryWeek = mysqli_query($con, "SELECT COUNT(*) as weeklyAppointments FROM tblappointment WHERE WEEK(AptDate) = WEEK(CURDATE()) AND Stylist = '$stylistName'");
$rowWeek = mysqli_fetch_assoc($queryWeek);
$weeklyAppointments = $rowWeek['weeklyAppointments'];

// Query for total appointments this month
$queryMonth = mysqli_query($con, "SELECT COUNT(*) as monthlyAppointments FROM tblappointment WHERE MONTH(AptDate) = MONTH(CURDATE()) AND Stylist = '$stylistName'");
$rowMonth = mysqli_fetch_assoc($queryMonth);
$monthlyAppointments = $rowMonth['monthlyAppointments'];

// Query for total appointments last month
$lastMonth = date('Y-m', strtotime('-1 month'));
$queryLastMonth = mysqli_query($con, "SELECT COUNT(*) as lastMonthAppointments FROM tblappointment WHERE AptDate LIKE '$lastMonth%' AND Stylist = '$stylistName'");
$rowLastMonth = mysqli_fetch_assoc($queryLastMonth);
$lastMonthAppointments = $rowLastMonth['lastMonthAppointments'];

// Calculate percentage change
if ($lastMonthAppointments > 0) {
    $percentageChange = (($monthlyAppointments - $lastMonthAppointments) / $lastMonthAppointments) * 100;
    $percentageChange = round($percentageChange, 2); // Rounding to two decimal places
} else {
    $percentageChange = $monthlyAppointments > 0 ? 100 : 0; // Avoid division by zero
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
$queryTotalAppointments = mysqli_query($con, "SELECT COUNT(*) as totalAppointments FROM tblappointment WHERE Stylist = '$stylistName'");
$rowTotalAppointments = mysqli_fetch_assoc($queryTotalAppointments);
$totalappointment = $rowTotalAppointments['totalAppointments'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Minell's Stylist</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/feather/feather.css">
    <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/favicon.png"/>

    <style>
    body {
        background-color: #f0f8ff; /* Light blue background for the page */
        color: #333; /* Darker font color for better readability */
    }

    #appointmentCalendar {
        max-width: 1200px; /* Set a maximum width */
        max-height: 400px; /* Set a maximum height */
        border-radius: 8px; /* Rounded corners */
        padding: 10px; /* Add some padding inside the calendar */
    }

    .fc {
        font-size: 0.8em; /* Adjust font size for the FullCalendar */
    }

    .fc-event {
        border-radius: 5px; /* Rounded corners for events */
        color: white; /* White text for event titles for better contrast */
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
                    <div class="row">
                        <div class="col-md-12 grid-margin">
                            <div class="row">
                            <div class="col-6 col-xl-8 mb-4 mb-xl-0">
                                    <h3 class="font-weight-bold">Welcome, <?php echo htmlspecialchars($stylistData['name']); ?></h3>
                                    <h6 class="font-weight-normal mb-0">Have a nice day ahead! You have <span class="text-primary"><?php echo $todayAppointments; ?> appointments today!</span></h6>
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
                                margin-right: 15px; /* Add spacing between items */
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
                                            $query = "SELECT Name, Services, AptTime FROM tblappointment WHERE AptDate = '$today' AND Stylist = '$stylistName'"; // 0 for Upcoming appointments
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
                                                    echo '          <small>' . date("h:i A", strtotime($row['AptTime'])) . '</small>'; // Format time to 12-hour format with AM/PM

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
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                                
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.php -->
                <?php include_once('partials/_footer.php'); ?>  
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
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>
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
                $queryAppointments = mysqli_query($con, "SELECT AptDate, AptTime, Name, Services, Status FROM tblappointment WHERE Stylist = '$stylistName'");
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
                        case '1':
                            $eventBackgroundColor = '#378006'; // Green for status 1
                            break;
                        case '2':
                            $eventBackgroundColor = '#ff0000'; // Red for status 2
                            break;
                        case '3':
                            $eventBackgroundColor = '#0000ff'; // Blue for status 3
                            break;
                        case '4':
                            $eventBackgroundColor = '#FFA500'; // Gray for no-show (status 4)
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
