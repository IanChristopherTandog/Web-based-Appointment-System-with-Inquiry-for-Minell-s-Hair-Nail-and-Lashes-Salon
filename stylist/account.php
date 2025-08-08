<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');

// Redirect if not logged in
if (!isset($_SESSION['salondbaid']) || strlen($_SESSION['salondbaid']) == 0) {
    header('location:../index.php');
    exit; // Ensure the script stops after redirection
}

// Fetch stylist's information using the session ID
$stylistId = $_SESSION['salondbaid'];
$stmt = $con->prepare("SELECT * FROM tblstylist WHERE id = ?");
$stmt->bind_param("i", $stylistId);
$stmt->execute();
$stylistData = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get stylist's name
$stylistName = $stylistData['name'];

// Fetch specialties from tblcategory
$stmt = $con->prepare("SELECT CategoryName FROM tblcategory");
$stmt->execute();
$categoryResult = $stmt->get_result();
$categories = [];

while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row['CategoryName'];
}
$stmt->close();

// Fetch current specialties for the stylist
$currentSpecialty = $stylistData['specialty']; // Already fetched with stylist data
$currentSpecialties = explode(',', $currentSpecialty); // Comma-separated specialties

// Update details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $specialties = $_POST['specialty']; // Selected specialties
    $specialty = implode(',', $specialties); // Convert to comma-separated string
    $availability = $_POST['availability']; // Selected days
    $availabilityString = implode(',', $availability); // Convert to comma-separated string

    // Initialize the base update query
    $updateQuery = "UPDATE tblstylist SET name=?, email=?, specialty=?, availability=?";

    // Handle password update if a new password is provided
    if (!empty($_POST['new_password'])) {
        $currentPassword = mysqli_real_escape_string($con, $_POST['current_password']);
        if (password_verify($currentPassword, $stylistData['password'])) {
            $newPassword = mysqli_real_escape_string($con, $_POST['new_password']);
            if (strlen($newPassword) >= 8) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateQuery .= ", password=?";
                $params = [$name, $email, $specialty, $availabilityString, $hashedPassword];
            } else {
                echo "<script>alert('Password must be at least 8 characters long.');</script>";
                exit;
            }
        } else {
            echo "<script>alert('Current password is incorrect.'); window.location.href='account.php';</script>";
            exit; // Prevent further execution
        }
    } else {
        $params = [$name, $email, $specialty, $availabilityString];
    }

    // Finalize the query with the WHERE clause
    $updateQuery .= " WHERE id=?";
    $params[] = $stylistId;

    // Prepare and execute the update query
    $stmt = $con->prepare($updateQuery);
    
    // Dynamically bind the parameters based on the number of parameters
    $stmt->bind_param(str_repeat('s', count($params) - 1) . 'i', ...$params);
    
    if ($stmt->execute()) {
        echo "<script>alert('Account details updated successfully!'); window.location.href='account.php';</script>";
    } else {
        echo "<script>alert('Failed to update account details: " . $stmt->error . "');</script>";
    }
    $stmt->close();
    exit;
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
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="vendors/select2/select2.min.css">
    <link rel="stylesheet" href="vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <!-- inject:css -->
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <link rel="shortcut icon" href="images/favicon.png" />
    <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.bootstrap5.css">
</head>

<body>
<div class="container-scroller">
    <?php include_once('partials/_navbar.php'); ?>
    <div class="container-fluid page-body-wrapper">
        
        <?php include_once('partials/_sidebar.php'); ?>
        <div class="main-panel">        
          <div class="content-wrapper">
            <div class="row">
                <div class="col-12 grid-margin">
                  <div class="card">
                      <div class="card-body">
                          <h4 class="card-title">Update Account Details</h4>
                          <form id="stylistSettingsForm" method="POST" action="account.php">
                              <p class="card-description">
                                  Personal Info
                              </p>
                              <div class="row">
                                  <div class="col-md-6">
                                      <div class="form-group">
                                          <label for="stylistName">Full Name</label>
                                          <input type="text" class="form-control" id="stylistName" name="name" value="<?php echo htmlspecialchars($stylistData['name']); ?>" required>
                                      </div>
                                      <div class="form-group">
                                          <label for="stylistEmail">Email</label>
                                          <input type="email" class="form-control" id="stylistEmail" name="email" value="<?php echo htmlspecialchars($stylistData['email']); ?>" required>
                                      </div>
                                      <div class="form-group">
                                          <label for="specialty">Specialty</label>
                                          <select class="js-example-basic-multiple w-100" name="specialty[]" multiple="multiple">
                                              <?php foreach ($categories as $category): ?>
                                                  <option value="<?php echo htmlspecialchars($category); ?>" <?php echo in_array($category, $currentSpecialties) ? 'selected' : ''; ?>><?php echo htmlspecialchars($category); ?></option>
                                              <?php endforeach; ?>
                                          </select>
                                      </div>
                                      <div class="form-group">
                                        <label for="availability">Availability</label>
                                        <select class="js-example-basic-multiple w-100" name="availability[]" multiple="multiple" id="availability" required>
                                            <option value="Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday" <?php echo in_array('Everyday', explode(',', $stylistData['availability'])) ? 'selected' : ''; ?>>Everyday</option>
                                            <option value="Monday" <?php echo in_array('Monday', explode(',', $stylistData['availability'])) ? 'selected' : ''; ?>>Monday</option>
                                            <option value="Tuesday" <?php echo in_array('Tuesday', explode(',', $stylistData['availability'])) ? 'selected' : ''; ?>>Tuesday</option>
                                            <option value="Wednesday" <?php echo in_array('Wednesday', explode(',', $stylistData['availability'])) ? 'selected' : ''; ?>>Wednesday</option>
                                            <option value="Thursday" <?php echo in_array('Thursday', explode(',', $stylistData['availability'])) ? 'selected' : ''; ?>>Thursday</option>
                                            <option value="Friday" <?php echo in_array('Friday', explode(',', $stylistData['availability'])) ? 'selected' : ''; ?>>Friday</option>
                                            <option value="Saturday" <?php echo in_array('Saturday', explode(',', $stylistData['availability'])) ? 'selected' : ''; ?>>Saturday</option>
                                            <option value="Sunday" <?php echo in_array('Sunday', explode(',', $stylistData['availability'])) ? 'selected' : ''; ?>>Sunday</option>
                                        </select>
                                    </div>
                                  </div>
                                  <div class="col-md-6">
                                  <h4 class="card-title">Update Password(Optional)</h4>
                                      <div class="form-group">
                                          <label for="currentPassword">Current Password</label>
                                          <input type="password" class="form-control" id="currentPassword" name="current_password" placeholder="Enter current password">
                                      </div>
                                      <div class="form-group">
                                          <label for="newPassword">New Password</label>
                                          <input type="password" class="form-control" id="newPassword" name="new_password" placeholder="Enter new password">
                                      </div>
                                  </div>
                              </div>
                              <input type="hidden" name="id" value="<?php echo htmlspecialchars($stylistData['id']); ?>">
                              <div class="form-group">
                                  <button type="button" class="btn btn-secondary" onclick="window.location.href='account.php'">Cancel</button>
                                  <button type="submit" class="btn btn-primary">Update Details</button>
                              </div>
                          </form>
                      </div>
                  </div>
              </div>
            </div>
          </div>
            <?php include_once('partials/_footer.php'); ?>
        </div>
    </div>
</div>
<!-- container-scroller -->

<!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="vendors/select2/select2.min.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>

    <script src="js/select2.js"></script>

<?php include_once('partials/logout.php');?>
</body>
</html>
