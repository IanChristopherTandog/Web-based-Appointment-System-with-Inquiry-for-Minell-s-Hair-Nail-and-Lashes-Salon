<?php
// Database connection
include('includes/dbconnection.php');
session_start();
error_reporting(E_ALL);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Initialize response array
$response = [
    'services' => '',
    'stylists' => ''
];

if (isset($_POST['serviceType']) && !empty($_POST['serviceType'])) {
    $serviceType = $_POST['serviceType'];
    
    // Prepare statement to fetch stylists based on specialty matching the selected service type
    $stmt = $con->prepare("SELECT name FROM tblstylist WHERE specialty LIKE ?");
    if (!$stmt) {
        die("SQL Error: " . $con->error);
    }
    $searchTerm = "%" . $serviceType . "%";
    $stmt->bind_param("s", $searchTerm);
    
    // Execute the statement
    $stmt->execute();
    
    // Get the result for stylists
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        while ($stylistRow = $result->fetch_assoc()) {
            $fullName = htmlspecialchars($stylistRow['name']);
            $response['stylists'] .= '<option value="' . $fullName . '">' . $fullName . '</option>';
        }
    } else {
        $response['stylists'] .= '<option value="">No stylists available</option>';
    }
    $stmt->close();

    // Fetch services based on the selected service type
    $query = $con->prepare("SELECT ServiceName, Duration, Cost, CategoryName, ImagePath, ServiceDescription FROM tblservices WHERE CategoryName = ?");
    if (!$query) {
        die("SQL Error: " . $con->error);
    }
    $query->bind_param("s", $serviceType);
    $query->execute();
    $serviceResult = $query->get_result();

    if ($serviceResult->num_rows > 0) {
        while ($row = $serviceResult->fetch_array()) {
            $response['services'] .= '<option value="' . htmlspecialchars($row['ServiceName']) . '" 
                  data-duration="' . htmlspecialchars($row['Duration']) . '" 
                  data-cost="' . htmlspecialchars($row['Cost']) . '" 
                  data-service-type="' . htmlspecialchars($row['CategoryName']) . '" 
                  data-image="' . htmlspecialchars($row['ImagePath']) . '" 
                  data-description="' . htmlspecialchars($row['ServiceDescription']) . '">' . 
                  htmlspecialchars($row['ServiceName']) . 
                  '</option>';
        }
    } else {
        $response['services'] .= '<option value="">No services found for this category</option>';
    }
    $query->close();
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);

// Close database connection
$con->close();
?>
