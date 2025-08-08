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
// Function to fetch total inquiries for the last 12 months
function getMonthlyInquiries($con) {
    $query = "
    SELECT 
        DATE_FORMAT(submit_date, '%Y-%m') AS month,
        COUNT(*) AS total_inquiries
    FROM tblinquiries
    WHERE submit_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY month
    ORDER BY month ASC";

    $result = mysqli_query($con, $query);
    $inquiriesData = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $inquiriesData[$row['month']] = $row['total_inquiries'];
        }
    } else {
        echo "Error: " . mysqli_error($con); // Debugging line
    }
    return $inquiriesData;
}

// Fetch monthly inquiries data
$inquiriesData = getMonthlyInquiries($con);
$salesMonths = getLast12Months(); // You should define this function to get the last 12 months
$inquiriesAmounts = array_map(fn($month) => $inquiriesData[$month] ?? 0, $salesMonths);

mysqli_close($con);
?>

<script>
    $(function() {
        'use strict';

        // Inquiries data from PHP
        var inquiriesAmounts = <?php echo json_encode($inquiriesAmounts); ?>; // Inquiries amounts

        // Line chart for monthly inquiries data
        if ($("#InquiriesLineChart").length) {
            var ctxInquiries = $("#InquiriesLineChart").get(0).getContext("2d");
            var inquiriesChart = new Chart(ctxInquiries, {
                type: 'line',
                data: {
                    labels: salesMonths, // Ensure you use the correct variable for labels
                    datasets: [{
                        label: 'Monthly Inquiries',
                        data: inquiriesAmounts,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
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
                                text: 'Number of Inquiries'
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
    });
</script>
