<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');
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
// Query for total online appointments
$queryOnlineAppointments = mysqli_query($con, "SELECT COUNT(*) AS totalOnlineAppointments FROM tblappointment WHERE Type = 'Online'");
$rowOnlineAppointments = mysqli_fetch_assoc($queryOnlineAppointments);
$totalOnlineAppointments = $rowOnlineAppointments['totalOnlineAppointments'];

// Query for total walk-in appointments
$queryWalkInAppointments = mysqli_query($con, "SELECT COUNT(*) AS totalWalkInAppointments FROM tblappointment WHERE Type = 'Walk-in'");
$rowWalkInAppointments = mysqli_fetch_assoc($queryWalkInAppointments);
$totalWalkInAppointments = $rowWalkInAppointments['totalWalkInAppointments'];

// Count total registered users
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

// Calculate percentage change for current month vs last month
if ($lastMonthCount > 0) {
    $percentageChange = (($currentMonthCount - $lastMonthCount) / $lastMonthCount) * 100;
} else {
    $percentageChange = $currentMonthCount > 0 ? 100 : 0; // 100% increase if last month count is 0
}

// Get the current year and last year
$currentYear = date('Y');
$lastYear = date('Y', strtotime('-1 year'));

// Query for total registered users this year
$queryThisYear = mysqli_query($con, "SELECT COUNT(*) AS total FROM tbluser WHERE YEAR(reg_date) = '$currentYear'");
$totalUsersThisYear = mysqli_fetch_assoc($queryThisYear)['total'];

// Query for total registered users last year
$queryLastYear = mysqli_query($con, "SELECT COUNT(*) AS total FROM tbluser WHERE YEAR(reg_date) = '$lastYear'");
$totalUsersLastYear = mysqli_fetch_assoc($queryLastYear)['total'];

// Calculate percentage change for this year vs last year
if ($totalUsersLastYear > 0) {
    $yearlyPercentageChange = (($totalUsersThisYear - $totalUsersLastYear) / $totalUsersLastYear) * 100;
} else {
    $yearlyPercentageChange = $totalUsersThisYear > 0 ? 100 : 0; // 100% increase if last year count is 0
}
// Query to get the count of active users in the last 30 days by email
$queryActiveUsers = mysqli_query($con, "SELECT COUNT(DISTINCT Email) AS active_users FROM tblappointment WHERE AptDate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
$activeUsersData = mysqli_fetch_assoc($queryActiveUsers);
$activeUsersCount = $activeUsersData['active_users'] ?? 0; // Default to 0 if null

// Total days in the period (e.g., 30 days)
$totalDays = 30;

// Calculate average active users per day
$averageActiveUsers = $activeUsersCount / $totalDays;


//Count total sum of Inquiries from both tblreplies and tblinquiry and display the percentage of comparison of inquiries from this month to last month
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

// Query for total inquiries from registered users
$queryRegisteredInquiries = mysqli_query($con, "SELECT COUNT(*) AS totalRegisteredInquiries FROM tblinquiry WHERE user_type = 'REGISTERED'");
$rowRegisteredInquiries = mysqli_fetch_assoc($queryRegisteredInquiries);
$totalRegisteredInquiries = $rowRegisteredInquiries['totalRegisteredInquiries'];

// Query for total inquiries from guest users
$queryGuestInquiries = mysqli_query($con, "SELECT COUNT(*) AS totalGuestInquiries FROM tblinquiry WHERE user_type = 'GUEST'");
$rowGuestInquiries = mysqli_fetch_assoc($queryGuestInquiries);
$totalGuestInquiries = $rowGuestInquiries['totalGuestInquiries'];

// Calculate total inquiries
$totalInquiriesAllTime = $totalRegisteredInquiries + $totalGuestInquiries;

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

// Sales calculations
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

// Get the current year and last year
$currentYear = date('Y');
$lastYear = date('Y', strtotime('-1 year'));

// Query for total sales this year
$queryThisYearSales = mysqli_query($con, "SELECT SUM(Price) AS totalSales FROM tblappointment WHERE YEAR(AptDate) = '$currentYear' AND Status = 3");
$totalSalesThisYearData = mysqli_fetch_assoc($queryThisYearSales);
$totalSalesThisYear = $totalSalesThisYearData['totalSales'] ?? 0; // Default to 0 if null

// Query for total sales last year
$queryLastYearSales = mysqli_query($con, "SELECT SUM(Price) AS totalSales FROM tblappointment WHERE YEAR(AptDate) = '$lastYear' AND Status = 3");
$totalSalesLastYearData = mysqli_fetch_assoc($queryLastYearSales);
$totalSalesLastYear = $totalSalesLastYearData['totalSales'] ?? 0; // Default to 0 if null

// Calculate percentage change for monthly sales
if ($lastMonthSales > 0) {
    $percentageChange = (($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100;
    $percentageChange = round($percentageChange, 2); // Rounding to two decimal places
    $inquiryMessage = $percentageChange > 0 ? sprintf("%.2f%% more than last month", $percentageChange) : sprintf("%.2f%% fewer than last month", abs($percentageChange));
} else {
    $percentageChange = $currentMonthSales > 0 ? 100 : 0; // 100% increase if last month count is 0
    $inquiryMessage = $currentMonthSales > 0 ? "100% more than last month" : "No sales this month.";
}
$currentMonth = date('n');

// Calculate average monthly sales
$averageMonthlySales = $currentMonth > 0 ? $totalSales / $currentMonth : 0; // Avoid division by zero

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
    <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">

    <!-- inject:css -->
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/favicon.png" />

    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.dataTables.css">
    
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
                    <div class="col-md-3 mb-4 stretch-card transparent">
                      <div class="card card-dark-blue">
                          <div class="card-body">
                              <a href="all-appointments.php" style="text-decoration: none; color: inherit;">
                                  <h4 class="mb-0">
                                      <i class="bi bi-calendar-check"></i>
                                      Total Appointments: <?php echo $totalappointment; ?>
                                  </h4>
                                  <br>
                                  <p>Online Appointments: <?php echo $totalOnlineAppointments; ?></p>
                                  <p>Walk-in Appointments: <?php echo $totalWalkInAppointments; ?></p>
                                  <p>Avg Appointments per Day: <?php echo round($totalappointment / date('t')); ?></p>
                              </a>
                          </div>
                      </div>
                  </div>

                  
                  <div class="col-md-3 mb-4 stretch-card transparent">
                      <div class="card card-tale">
                          <div class="card-body">
                              <a href="inquiries.php" style="text-decoration: none; color: inherit;">
                                  <h4 class="mb-0">
                                      <i class="bi bi-chat-dots"></i> Total Inquiries: <?= $totalInquiriesAllTime ?>
                                  </h4><br>
                                  <p>Registered Users Inquiries: <?= $totalRegisteredInquiries ?></p>
                                  <p>Guest Users Inquiries: <?= $totalGuestInquiries ?></p>
                                  <p>Avg Inquiries per Day: <?php echo round($totalInquiriesAllTime / date('t')); ?></p>
                              </a>
                          </div>
                      </div>
                  </div>
                  
                  <div class="col-md-3 mb-4 stretch-card transparent">
                      <div class="card card-light-danger">
                          <div class="card-body">
                              <a href="clients.php" style="text-decoration: none; color: inherit;">
                                  <h4 class="mb-0">
                                      <i class="bi bi-people"></i> Registered Clients: <?php echo $totalcust; ?>
                                  </h4> 
                                  <br>
                                  <p>Total Users This Year: <?php echo $totalUsersThisYear; ?></p>
                                  <p>Total Users Last Year: <?php echo $totalUsersLastYear; ?></p>
                                  <p>Avg Active Users (Last 30 Days): <?php echo round($averageActiveUsers, 2); ?></p>
                              </a>
                          </div>
                      </div>
                  </div>

                  <div class="col-md-3 mb-4 stretch-card transparent">
                    <div class="card card-light-blue">
                        <div class="card-body">
                            <a href="reports.php" style="text-decoration: none; color: inherit;">
                                <h4 class="mb-0">
                                    <i class="bi bi-cash-coin"></i> Total Sales: ₱<?php echo number_format($totalSales, 2); ?>
                                </h4>
                                <br>
                                <p>Total Sales This Year: ₱<?php echo number_format($totalSalesThisYear, 2); ?></p>
                                <p>Total Sales Last Year: ₱<?php echo number_format($totalSalesLastYear, 2); ?></p>
                                <!-- <p>Avg Daily Sales: ₱<?php echo number_format($totalSales / date('z'), 2); ?></p> -->
                                <p>Avg Monthly Sales: ₱<?php echo number_format($averageMonthlySales, 2); ?></p>
                            </a>
                        </div>
                    </div>
                </div>
              </div>
              
              
              <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
            
                    <?php include_once('reports/appointmentReport.php'); ?>
            
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title text-center">Total Appointments</h4>
                      <canvas id="TotalAppointmentsLineChart"></canvas>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title text-center">Online and Walk-in per Month</h4>
                      <canvas id="AppointmentLineChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-lg-6 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title text-center">Total Online and Walk-in</h4>
                      <canvas id="AppointmentpieChart"></canvas>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title text-center">Most Booked Services</h4>
                      <canvas id="ServicePopularityChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-6 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title text-center">Most Booked Stylist</h4>
                      <canvas id="StylistPopularityChart"></canvas>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title text-center">Appointments Status</h4>
                      <canvas id="StatusPieChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-lg-6 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title text-center">Monthly Total Sales</h4>
                      <canvas id="SalesLineChart"></canvas>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title text-center">Weekly Total Sales</h4>
                      <canvas id="WeeklySalesLineChart"></canvas>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>
    <script src="js/chart.js"></script>
    
    <script src="js/dataTables.select.min.js"></script>
    <!-- inject:js -->
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>

    <!-- endinject -->
    <!-- Custom js for this page -->

    <script src="js/Chart.roundedBarCharts.js"></script>
        
    <!-- Link to jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

    <!-- Link to DataTables core JavaScript -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

    <!-- Link to DataTables Buttons plugin -->
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.js"></script>

    <!-- Link to DataTables Buttons DataTables plugin -->
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.dataTables.js"></script>

    <!-- Link to JSZip library (used for Excel export) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- Link to PDFMake library (used for PDF export) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>

    <!-- Link to PDFMake virtual file system fonts (needed for PDF export) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <!-- Link to DataTables Buttons HTML5 export (e.g., Excel, CSV, PDF) -->
    <script src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.html5.min.js"></script>

    <!-- Link to DataTables Buttons Print export -->
    <script src="https://cdn.datatables.net /buttons/3.2.0/js/buttons.print.min.js"></script>

    <script>
        new DataTable('#example', {
            layout: {
                topStart: {
                    buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                }
            }
        });
    </script>
     <!-- container-scroller -->
    <!-- chart.php -->
    <?php include_once('reports/salesCharts.php'); ?>
    <?php include_once('reports/appointmentCharts.php'); ?>

    
</body>
</html>
