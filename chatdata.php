<?php 
include('client/includes/dbconnection.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch categories and sort alphabetically
$category_query = "SELECT CategoryName FROM tblcategory ORDER BY CategoryName ASC";
$category_result = $con->query($category_query);

// Fetch services and sort alphabetically by ServiceName
$service_query = "SELECT ServiceName, Cost, CategoryName, ServiceDescription FROM tblservices ORDER BY ServiceName ASC";
$service_result = $con->query($service_query);

// Fetch stylists and sort alphabetically by first_name
$stylist_query = "SELECT name, specialty FROM tblstylist ORDER BY name ASC";
$stylist_result = $con->query($stylist_query);

// Fetch appointments with limited columns
$appointment_query = "SELECT AptDate, AptTime, Services, ApplyDate, Status, Stylist, Type FROM tblappointment ORDER BY AptDate DESC";
$appointment_result = $con->query($appointment_query);

// Fetch FAQs
$faq_query = "SELECT question, answer FROM tblfaqs ORDER BY created_at DESC";
$faq_result = $con->query($faq_query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatBot Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        section {
            margin-bottom: 30px;
        }
        h2 {
            color: #333;
        }
        p {
            margin: 5px 0;
        }
        hr {
            border: 0;
            border-top: 1px solid #ddd;
            margin: 15px 0;
        }
    </style>
</head>
<body>

    <!-- Categories Section -->
    <section>
        <h2>Categories</h2>
        <?php while ($row = $category_result->fetch_assoc()) { ?>
            <p><strong>Category Name:</strong> <?php echo htmlspecialchars(empty($row['CategoryName']) ? 'NULL' : $row['CategoryName']); ?></p>
            <hr>
        <?php } ?>
    </section>

    <!-- Services Section -->
    <section>
        <h2>Services</h2>
        <?php while ($row = $service_result->fetch_assoc()) { ?>
            <p><strong>Service Name:</strong> <?php echo htmlspecialchars(empty($row['ServiceName']) ? 'NULL' : $row['ServiceName']); ?></p>
            <p><strong>Cost:</strong> <?php echo htmlspecialchars(empty($row['Cost']) ? 'NULL' : $row['Cost']); ?></p>
            <p><strong>Category:</strong> <?php echo htmlspecialchars(empty($row['CategoryName']) ? 'NULL' : $row['CategoryName']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars(empty($row['ServiceDescription']) ? 'NULL' : $row['ServiceDescription']); ?></p>
            <hr>
        <?php } ?>
    </section>

    <!-- Stylists Section -->
    <section>
        <h2>Stylists</h2>
        <?php while ($row = $stylist_result->fetch_assoc()) { ?>
            <p><strong>Name:</strong> <?php echo htmlspecialchars(empty($row['name']) ? 'NULL' : $row['name']); ?></p>
            <p><strong>Specialty:</strong> <?php echo htmlspecialchars(empty($row['specialty']) ? 'NULL' : $row['specialty']); ?></p>
            <hr>
        <?php } ?>
    </section>

    <!-- Appointments Section -->
    <section>
        <h2>Appointments</h2>
        <?php while ($row = $appointment_result->fetch_assoc()) { ?>
            <p><strong>Date:</strong> <?php echo htmlspecialchars(empty($row['AptDate']) ? 'NULL' : $row['AptDate']); ?></p>
            <p><strong>Time:</strong> <?php echo htmlspecialchars(empty($row['AptTime']) ? 'NULL' : $row['AptTime']); ?></p>
            <p><strong>Services:</strong> <?php echo htmlspecialchars(empty($row['Services']) ? 'NULL' : $row['Services']); ?></p>
            <p><strong>Apply Date:</strong> <?php echo htmlspecialchars(empty($row['ApplyDate']) ? 'NULL' : $row['ApplyDate']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars(empty($row['Status']) ? 'NULL' : $row['Status']); ?></p>
            <p><strong>Stylist:</strong> <?php echo htmlspecialchars(empty($row['Stylist']) ? 'NULL' : $row['Stylist']); ?></p>
            <p><strong>Type:</strong> <?php echo htmlspecialchars(empty($row['Type']) ? 'NULL' : $row['Type']); ?></p>
            <hr>
        <?php } ?>
    </section>

    <!-- FAQs Section -->
    <section>
        <h2>FAQs</h2>
        <?php while ($row = $faq_result->fetch_assoc()) { ?>
            <p><strong>Question:</strong> <?php echo htmlspecialchars(empty($row['question']) ? 'NULL' : $row['question']); ?></p>
            <p><strong>Answer:</strong> <?php echo htmlspecialchars(empty($row['answer']) ? 'NULL' : $row['answer']); ?></p>
            <hr>
        <?php } ?>
    </section>

    <!-- Page Section -->
    <section>
        <h2>Page</h2>
        <?php 
        // Fetch tblpage data
        $tblpage_query = "SELECT PageType, PageTitle, PageDescription, Email, MobileNumber, Timing, address, Vision, History FROM tblpage";
        $tblpage_result = $con->query($tblpage_query);

        while ($row = $tblpage_result->fetch_assoc()) { ?>
            <div>
                <p><strong>Page Type:</strong> <?php echo !empty($row['PageType']) ? htmlspecialchars($row['PageType']) : 'NULL'; ?></p>
                <p><strong>Page Title:</strong> <?php echo !empty($row['PageTitle']) ? htmlspecialchars($row['PageTitle']) : 'NULL'; ?></p>
                <p><strong>Page Description:</strong> <?php echo !empty($row['PageDescription']) ? htmlspecialchars($row['PageDescription']) : 'NULL'; ?></p>
                <p><strong>Email:</strong> <?php echo !empty($row['Email']) ? htmlspecialchars($row['Email']) : 'NULL'; ?></p>
                <p><strong>Mobile Number:</strong> <?php echo !empty($row['MobileNumber']) ? htmlspecialchars($row['MobileNumber']) : 'NULL'; ?></p>
                <p><strong>Working Hours:</strong> <?php echo !empty($row['Timing']) ? htmlspecialchars($row['Timing']) : 'NULL'; ?></p>
                <p><strong>Address:</strong> <?php echo !empty($row['address']) ? htmlspecialchars($row['address']) : 'NULL'; ?></p>
                <p><strong>Vision:</strong> <?php echo !empty($row['Vision']) ? htmlspecialchars($row['Vision']) : 'NULL'; ?></p>
                <p><strong>History:</strong> <?php echo !empty($row['History']) ? htmlspecialchars($row['History']) : 'NULL'; ?></p>
                <hr>
            </div>
        <?php } ?>
    </section>

    <?php
    // Fetch day offs from tbldayoffs and sort by start_date
    $availability_query = "SELECT title, start_date FROM tbldayoffs ORDER BY start_date ASC";
    $availability_result = $con->query($availability_query);
    ?>

    <!-- Availability Section -->
    <section>
        <h2>Availability</h2>
        <?php while ($row = $availability_result->fetch_assoc()) { ?>
            <p><strong>Title:</strong> <?php echo htmlspecialchars(empty($row['title']) ? 'NULL' : $row['title']); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars(empty($row['start_date']) ? 'NULL' : $row['start_date']); ?></p>
            <hr>
        <?php } ?>
    </section>

</body>
</html>