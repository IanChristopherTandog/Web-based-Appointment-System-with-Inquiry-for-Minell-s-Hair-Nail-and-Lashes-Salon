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

// Function to fetch monthly sales for the last 12 months
function getMonthlySales($con) {
    $query = "
    SELECT 
        DATE_FORMAT(ApplyDate, '%Y-%m') AS month,
        SUM(Price) AS monthly_sales
    FROM tblappointment
    WHERE Status = 3 AND ApplyDate >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY month
    ORDER BY month ASC";

    $result = mysqli_query($con, $query);
    $monthlySales = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $monthlySales[$row['month']] = $row['monthly_sales'];
        }
    } else {
        echo "Error: " . mysqli_error($con); // Debugging line
    }
    return $monthlySales;
}

// Function to fetch weekly sales for the last 12 weeks
function getWeeklySales($con) {
    $query = "
    SELECT 
        DATE_FORMAT(ApplyDate, '%Y-%u') AS week,
        SUM(Price) AS weekly_sales
    FROM tblappointment
    WHERE Status = 3 AND ApplyDate >= DATE_SUB(NOW(), INTERVAL 12 WEEK)
    GROUP BY week
    ORDER BY week ASC";

    $result = mysqli_query($con, $query);
    $weeklySales = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $weeklySales[$row['week']] = $row['weekly_sales'];
        }
    } else {
        echo "Error: " . mysqli_error($con); // Debugging line
    }
    return $weeklySales;
}

// Generate the last 12 months
function getLast12Months() {
    $months = [];
    for ($i = 0; $i < 12; $i++) {
        $months[] = date('Y-m', strtotime("-$i months"));
    }
    return array_reverse($months);
}

// Generate the last 12 weeks
function getLast12Weeks() {
    $weeks = [];
    for ($i = 0; $i < 12; $i++) {
        $weeks[] = date('Y-W', strtotime("-$i weeks"));
    }
    return array_reverse($weeks);
}

$monthlySalesData = getMonthlySales($con);
$salesMonths = getLast12Months();
$salesAmounts = array_map(fn($month) => $monthlySalesData[$month] ?? 0, $salesMonths);

$weeklySalesData = getWeeklySales($con);
$salesWeeks = getLast12Weeks();
$weeklyAmounts = array_map(fn($week) => $weeklySalesData[$week] ?? 0, $salesWeeks);

mysqli_close($con);
?>

<script>
    $(function() {
        'use strict';

        // Sales data from PHP
        var salesMonths = <?php echo json_encode($salesMonths); ?>;
        var salesAmounts = <?php echo json_encode($salesAmounts); ?>;
        
        var salesWeeks = <?php echo json_encode($salesWeeks); ?>;
        var weeklyAmounts = <?php echo json_encode($weeklyAmounts); ?>;

        // Line chart for monthly sales data
        if ($("#SalesLineChart").length) {
            var ctx = $("#SalesLineChart").get(0).getContext("2d");
            var salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: salesMonths,
                    datasets: [{
                        label: 'Monthly Sales',
                        data: salesAmounts,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderWidth: 2,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Sales Amount ($)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Month'
                            }
                        }
                    }
                }
            });
        }

        // Line chart for weekly sales data
        if ($("#WeeklySalesLineChart").length) {
            var ctxWeekly = $("#WeeklySalesLineChart").get(0).getContext("2d");
            var weeklySalesChart = new Chart(ctxWeekly, {
                type: 'line',
                data: {
                    labels: salesWeeks,
                    datasets: [{
                        label: 'Weekly Sales',
                        data: weeklyAmounts,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderWidth: 2,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Sales Amount ($)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Week'
                            }
                        }
                    }
                }
            });
        }
    });
</script>
