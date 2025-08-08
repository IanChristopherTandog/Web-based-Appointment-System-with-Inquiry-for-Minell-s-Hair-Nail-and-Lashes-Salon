<?php
// Define connection parameters for both live and local environments
$live_host = "localhost";
$live_user = "u910139511_root";
$live_password = "minellssal0nDB!";
$live_database = "u910139511_salondb";

$local_host = "localhost";
$local_user = "root";
$local_password = ""; // Use the password set for your local MySQL server, or leave blank if none
$local_database = "salondb"; // Adjust the database name for your local environment if different

// Check if the script is running on a local server or live server
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    // Connect using local environment credentials
    $con = mysqli_connect($local_host, $local_user, $local_password, $local_database);
} else {
    // Connect using live server credentials
    $con = mysqli_connect($live_host, $live_user, $live_password, $live_database);
}

// Check the connection
if (mysqli_connect_errno()) {
    echo "Connection failed: " . mysqli_connect_error();
    exit();
}

// Function to fetch appointment counts for types
function getAppointmentCounts($con) {
    $query = "SELECT 
                SUM(CASE WHEN Type = 'Walk-in' THEN 1 ELSE 0 END) AS walk_in_count,
                SUM(CASE WHEN Type = 'Online' THEN 1 ELSE 0 END) AS online_count
              FROM tblappointment";
              
    $result = mysqli_query($con, $query);
    if ($result) {
        return mysqli_fetch_assoc($result);
    } else {
        return null;
    }
}

// Function to fetch monthly appointment counts for the last 12 months
function getMonthlyAppointmentCounts($con) {
    $query = "
    SELECT 
        DATE_FORMAT(AptDate, '%Y-%m') AS month,
        SUM(CASE WHEN Type = 'Walk-in' THEN 1 ELSE 0 END) AS walk_in_count,
        SUM(CASE WHEN Type = 'Online' THEN 1 ELSE 0 END) AS online_count
    FROM tblappointment
    WHERE AptDate >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY month
    ORDER BY month ASC";

    $result = mysqli_query($con, $query);
    if ($result) {
        $monthlyCounts = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $monthlyCounts[] = $row;
        }
        return $monthlyCounts;
    } else {
        return null;
    }
}

// Function to fetch total appointment counts for the last 12 months
function getTotalAppointmentCounts($con) {
    $query = "
    SELECT 
        DATE_FORMAT(AptDate, '%Y-%m') AS month,
        COUNT(*) AS total_count
    FROM tblappointment
    WHERE AptDate >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY month
    ORDER BY month ASC";

    $result = mysqli_query($con, $query);
    if ($result) {
        $totalCounts = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $totalCounts[] = $row;
        }
        return $totalCounts;
    } else {
        return null;
    }
}

// Generate labels for the past twelve months
function getLastTwelveMonthsLabels() {
    $labels = [];
    $currentDate = new DateTime();
    for ($i = 11; $i >= 0; $i--) {
        $month = $currentDate->modify("-1 month")->format('Y-m');
        $labels[] = $month;
    }
    return array_reverse($labels);
}

$counts = getAppointmentCounts($con);
$monthlyCounts = getMonthlyAppointmentCounts($con);
$totalMonthlyCounts = getTotalAppointmentCounts($con);

// Prepare data for the line chart
$months = getLastTwelveMonthsLabels();
$walkInCounts = array_fill(0, count($months), 0);
$onlineCounts = array_fill(0, count($months), 0);
$totalCounts = array_fill(0, count($months), 0);

// Fill in the counts for the months that have data
foreach ($monthlyCounts as $count) {
    $index = array_search($count['month'], $months);
    if ($index !== false) {
        $walkInCounts[$index] = $count['walk_in_count'];
        $onlineCounts[$index] = $count['online_count'];
    }
}

// Fill in total counts for the months that have data
foreach ($totalMonthlyCounts as $count) {
    $index = array_search($count['month'], $months);
    if ($index !== false) {
        $totalCounts[$index] = $count['total_count'];
    }
}

// Determine the highest count for the y-axis
$maxWalkIn = max($walkInCounts);
$maxOnline = max($onlineCounts);
$maxTotal = max($totalCounts);
$maxCount = max($maxWalkIn, $maxOnline, $maxTotal);

// Function to fetch the top 10 most popular services
function getServicePopularity($con) {
  $query = "SELECT Services, COUNT(*) AS count 
            FROM tblappointment 
            GROUP BY Services 
            ORDER BY count DESC 
            LIMIT 10";
  $result = mysqli_query($con, $query);
  
  $services = [];
  $counts = [];
  
  if ($result) {
      while ($row = mysqli_fetch_assoc($result)) {
          $services[] = $row['Services'];
          $counts[] = $row['count'];
      }
  }
  
  return ['services' => $services, 'counts' => $counts];
}


$serviceData = getServicePopularity($con);

// Function to fetch the top 10 most popular stylists by appointment count
function getTopStylists($con) {
  $query = "SELECT Stylist, COUNT(*) AS count 
            FROM tblappointment 
            GROUP BY Stylist 
            ORDER BY count DESC 
            LIMIT 10";
  $result = mysqli_query($con, $query);
  
  $stylists = [];
  $counts = [];
  
  if ($result) {
      while ($row = mysqli_fetch_assoc($result)) {
          $stylists[] = $row['Stylist'];
          $counts[] = $row['count'];
      }
  }
  
  return ['stylists' => $stylists, 'counts' => $counts];
}

$stylistData = getTopStylists($con);


// Function to fetch counts for each status
function getStatusCounts($con) {
  $query = "SELECT 
              SUM(CASE WHEN Status = '0' THEN 1 ELSE 0 END) AS upcoming_count,
              SUM(CASE WHEN Status = '1' THEN 1 ELSE 0 END) AS ongoing_count,
              SUM(CASE WHEN Status = '2' THEN 1 ELSE 0 END) AS cancelled_count,
              SUM(CASE WHEN Status = '3' THEN 1 ELSE 0 END) AS completed_count,
              SUM(CASE WHEN Status = '4' THEN 1 ELSE 0 END) AS no_show_count
            FROM tblappointment";
            
  $result = mysqli_query($con, $query);
  if ($result) {
      return mysqli_fetch_assoc($result);
  } else {
      return null;
  }
}

$statusCounts = getStatusCounts($con);

mysqli_close($con);
?>
<script>
    $(function() {
        'use strict';

        // Data fetched from the PHP script
        var walkInCount = <?php echo $counts['walk_in_count']; ?>;
        var onlineCount = <?php echo $counts['online_count']; ?>;

        // Pie chart data
        var doughnutPieData = {
            datasets: [{
                data: [walkInCount, onlineCount],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',  // Walk-in
                    'rgba(54, 162, 235, 0.5)'   // Online
                ],
                borderColor: [
                    'rgba(255,99,132,1)',        // Walk-in
                    'rgba(54, 162, 235, 1)'     // Online
                ],
            }],
            labels: [
                'Walk-in Appointments',
                'Online Appointments'
            ]
        };

        var doughnutPieOptions = {
            responsive: true,
            animation: {
                animateScale: true,
                animateRotate: true
            }
        };

        // Render the pie chart
        if ($("#AppointmentpieChart").length) {
            var pieChartCanvas = $("#AppointmentpieChart").get(0).getContext("2d");
            var pieChart = new Chart(pieChartCanvas, {
                type: 'pie',
                data: doughnutPieData,
                options: doughnutPieOptions
            });
        }

        // Data fetched from the PHP script
        var services = <?php echo json_encode($serviceData['services']); ?>;
        var counts = <?php echo json_encode($serviceData['counts']); ?>;

        // Pie chart data
        var servicePopularityData = {
            datasets: [{
                data: counts,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',   // Color for service 1
                    'rgba(54, 162, 235, 0.5)',   // Color for service 2
                    'rgba(255, 206, 86, 0.5)',   // Color for service 3
                    'rgba(75, 192, 192, 0.5)',   // Color for service 4
                    'rgba(153, 102, 255, 0.5)',  // Color for service 5
                    'rgba(255, 159, 64, 0.5)',   // Color for service 6
                    'rgba(201, 203, 207, 0.5)',  // Color for service 7
                    'rgba(255, 99, 71, 0.5)',    // Color for service 8
                    'rgba(100, 181, 246, 0.5)',  // Color for service 9
                    'rgba(144, 238, 144, 0.5)'   // Color for service 10
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',     // Border color for service 1
                    'rgba(54, 162, 235, 1)',     // Border color for service 2
                    'rgba(255, 206, 86, 1)',     // Border color for service 3
                    'rgba(75, 192, 192, 1)',     // Border color for service 4
                    'rgba(153, 102, 255, 1)',    // Border color for service 5
                    'rgba(255, 159, 64, 1)',     // Border color for service 6
                    'rgba(201, 203, 207, 1)',    // Border color for service 7
                    'rgba(255, 99, 71, 1)',      // Border color for service 8
                    'rgba(100, 181, 246, 1)',    // Border color for service 9
                    'rgba(144, 238, 144, 1)'     // Border color for service 10
                ],
            }],
            labels: services
        };

        var servicePopularityOptions = {
            responsive: true,
            animation: {
                animateScale: true,
                animateRotate: true
            }
        };

        // Render the pie chart
        if ($("#ServicePopularityChart").length) {
            var pieChartCanvas = $("#ServicePopularityChart").get(0).getContext("2d");
            var pieChart = new Chart(pieChartCanvas, {
                type: 'pie',
                data: servicePopularityData,
                options: servicePopularityOptions
            });
        }

        // Data for the stylist popularity pie chart
        var stylistNames = <?php echo json_encode($stylistData['stylists']); ?>;
        var stylistCounts = <?php echo json_encode($stylistData['counts']); ?>;

        var stylistPopularityData = {
            datasets: [{
                data: stylistCounts,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',   // Color for stylist 1
                    'rgba(54, 162, 235, 0.5)',   // Color for stylist 2
                    'rgba(255, 206, 86, 0.5)',   // Color for stylist 3
                    'rgba(75, 192, 192, 0.5)',   // Color for stylist 4
                    'rgba(153, 102, 255, 0.5)',  // Color for stylist 5
                    'rgba(255, 159, 64, 0.5)',   // Color for stylist 6
                    'rgba(201, 203, 207, 0.5)',  // Color for stylist 7
                    'rgba(255, 99, 71, 0.5)',    // Color for stylist 8
                    'rgba(100, 181, 246, 0.5)',  // Color for stylist 9
                    'rgba(144, 238, 144, 0.5)'   // Color for stylist 10
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',     // Border color for stylist 1
                    'rgba(54, 162, 235, 1)',     // Border color for stylist 2
                    'rgba(255, 206, 86, 1)',     // Border color for stylist 3
                    'rgba(75, 192, 192, 1)',     // Border color for stylist 4
                    'rgba(153, 102, 255, 1)',    // Border color for stylist 5
                    'rgba(255, 159, 64, 1)',     // Border color for stylist 6
                    'rgba(201, 203, 207, 1)',    // Border color for stylist 7
                    'rgba(255, 99, 71, 1)',      // Border color for stylist 8
                    'rgba(100, 181, 246, 1)',    // Border color for stylist 9
                    'rgba(144, 238, 144, 1)'     // Border color for stylist 10
                ],
            }],
            labels: stylistNames
        };

        var stylistPopularityOptions = {
            responsive: true,
            animation: {
                animateScale: true,
                animateRotate: true
            }
        };

        // Render the stylist popularity pie chart
        if ($("#StylistPopularityChart").length) {
            var pieChartCanvas = $("#StylistPopularityChart").get(0).getContext("2d");
            var pieChart = new Chart(pieChartCanvas, {
                type: 'pie',
                data: stylistPopularityData,
                options: stylistPopularityOptions
            });
        }

        // Status data from PHP
          var statusData = {
              datasets: [{
                  data: [
                      <?php echo $statusCounts['upcoming_count']; ?>,
                      <?php echo $statusCounts['ongoing_count']; ?>,
                      <?php echo $statusCounts['cancelled_count']; ?>,
                      <?php echo $statusCounts['completed_count']; ?>,
                      <?php echo $statusCounts['no_show_count']; ?>
                  ],
                  backgroundColor: [
                      'rgba(255, 165, 0, 0.5)',  // Orange for Upcoming
                      'rgba(0, 128, 0, 0.5)',    // Green for Ongoing
                      'rgba(255, 0, 0, 0.5)',    // Red for Cancelled
                      'rgba(0, 0, 255, 0.5)',    // Blue for Completed
                      'rgba(128, 128, 128, 0.5)' // Grey for No-Show
                  ],
                  borderColor: [
                      'rgba(255, 165, 0, 1)',    // Border Orange
                      'rgba(0, 128, 0, 1)',      // Border Green
                      'rgba(255, 0, 0, 1)',      // Border Red
                      'rgba(0, 0, 255, 1)',      // Border Blue
                      'rgba(128, 128, 128, 1)'   // Border Grey
                  ],
              }],
              labels: ['Upcoming', 'Ongoing', 'Cancelled', 'Completed', 'No-Show']
          };

          var statusOptions = {
              responsive: true,
              animation: {
                  animateScale: true,
                  animateRotate: true
              }
          };

          // Render the status pie chart
          if ($("#StatusPieChart").length) {
              var pieChartCanvas = $("#StatusPieChart").get(0).getContext("2d");
              var pieChart = new Chart(pieChartCanvas, {
                  type: 'pie',
                  data: statusData,
                  options: statusOptions
              });
          }
        // Line chart data for appointment types
        var months = <?php echo json_encode($months); ?>;
        var walkInCounts = <?php echo json_encode($walkInCounts); ?>;
        var onlineCounts = <?php echo json_encode($onlineCounts); ?>;

        var lineChartData = {
            labels: months,
            datasets: [
                {
                    label: 'Walk-in Appointments',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    data: walkInCounts,
                    fill: true,
                    tension: 0.1 // smooth lines
                },
                {
                    label: 'Online Appointments',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    data: onlineCounts,
                    fill: true,
                    tension: 0.1 // smooth lines
                }
            ]
        };

        var lineChartOptions = {
            responsive: true,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Months'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Number of Appointments'
                    },
                    suggestedMin: 0,
                    suggestedMax: <?php echo $maxCount; ?>, // Set y-axis max based on highest count
                }
            }
        };

        // Render the line chart for appointment types
        if ($("#AppointmentLineChart").length) {
            var lineChartCanvas = $("#AppointmentLineChart").get(0).getContext("2d");
            var lineChart = new Chart(lineChartCanvas, {
                type: 'line',
                data: lineChartData,
                options: lineChartOptions
            });
        }

        // Line chart data for total appointments
        var totalCounts = <?php echo json_encode($totalCounts); ?>;

        var totalAppointmentsData = {
            labels: months,
            datasets: [
                {
                    label: 'Total Appointments',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    data: totalCounts,
                    fill: true,
                    tension: 0.1 // smooth lines
                }
            ]
        };

        // Render the line chart for total appointments
        if ($("#TotalAppointmentsLineChart").length) {
            var totalAppointmentsCanvas = $("#TotalAppointmentsLineChart").get(0).getContext("2d");
            var totalAppointmentsChart = new Chart(totalAppointmentsCanvas, {
                type: 'line',
                data: totalAppointmentsData,
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Months'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Total Number of Appointments'
                            },
                            suggestedMin: 0,
                            suggestedMax: Math.max(...totalCounts) // Set y-axis max based on highest count
                        }
                    }
                }
            });
        }
    });
    </script>