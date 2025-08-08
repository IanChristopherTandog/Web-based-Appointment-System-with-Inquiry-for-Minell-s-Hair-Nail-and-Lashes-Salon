<?php   
session_start();
error_reporting(0);
ini_set('display_errors', 1);
include('partials/dbconnection.php');

// Handle the maintenance mode toggle request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $status = intval($_POST['status']);
    $query = $con->prepare("UPDATE tblsettings SET maintenance_mode = ?");
    if ($query === false) {
        die("MySQL prepare statement error: " . $con->error);
    }

    $query->bind_param("i", $status);
    if (!$query->execute()) {
        die("MySQL execution error: " . $query->error);
    } else {
        echo "Maintenance mode updated successfully!";
    }
    $query->close();
    exit;
}

// Refresh the page if a day off was added today
if (isset($_SESSION['last_day_off_added'])) {
    $lastDayOffDate = $_SESSION['last_day_off_added'];
    if (date('Y-m-d') != $lastDayOffDate) {
        // Reset the session variable to today's date
        $_SESSION['last_day_off_added'] = date('Y-m-d');
        echo "<script>location.reload();</script>"; // Refresh the page
    }
}

// Handle day off scheduling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['dayOffTitle'], $_POST['startDate']) && !isset($_POST['id'])) {
        // Insert new day off
        $title = $_POST['dayOffTitle'];
        $startDate = $_POST['startDate'];

        // Insert the day off data into the database
        $insertQuery = $con->prepare("INSERT INTO tbldayoffs (title, start_date) VALUES (?, ?)");
        if ($insertQuery === false) {
            die("MySQL prepare statement error: " . $con->error);
        }

        $insertQuery->bind_param("ss", $title, $startDate);
        if (!$insertQuery->execute()) {
            die("MySQL execution error: " . $insertQuery->error);
        } else {
            $_SESSION['last_day_off_added'] = date('Y-m-d'); // Store the date of last added day off
            echo "<script>alert('Day off scheduled successfully!');</script>";
            echo "<script>window.location.href = 'offday.php';</script>";
        }

        $insertQuery->close();
        exit;

    } elseif (isset($_POST['id'], $_POST['title'], $_POST['start_date'])) {
        // Update existing day off
        $id = $_POST['id'];
        $title = $_POST['title'];
        $startDate = $_POST['start_date'];

        // Update the day off in the database
        $updateQuery = $con->prepare("UPDATE tbldayoffs SET title=?, start_date=? WHERE id=?");
        if ($updateQuery === false) {
            die("MySQL prepare statement error: " . $con->error);
        }

        $updateQuery->bind_param("ssi", $title, $startDate, $id);
        if (!$updateQuery->execute()) {
            die("MySQL execution error: " . $updateQuery->error);
        } else {
            echo "<script>alert('Event has been updated.');</script>";
            echo "<script>window.location.href = 'offday.php';</script>"; // Redirect to offday.php
        }

        $updateQuery->close();
        exit;
    }
}
// Fetch the current maxAppointmentsPerDay value
$query = $con->prepare("SELECT maxAppointmentsPerDay FROM tblsettings LIMIT 1");
if ($query === false) {
    die("MySQL prepare statement error: " . $con->error);
}
$query->execute();
$query->bind_result($maxAppointmentsPerDay);
$query->fetch();
$query->close();

// Handle the update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['maxAppointmentsPerDay'])) {
    $maxAppointmentsPerDay = intval($_POST['maxAppointmentsPerDay']);
    
    // Update the value in the database
    $updateQuery = $con->prepare("UPDATE tblsettings SET maxAppointmentsPerDay = ? WHERE id = 1");
    if ($updateQuery === false) {
        die("MySQL prepare statement error: " . $con->error);
    }

    $updateQuery->bind_param("i", $maxAppointmentsPerDay);
    if (!$updateQuery->execute()) {
        die("MySQL execution error: " . $updateQuery->error);
    } else {
        echo "<script>alert('Max appointments per day updated successfully!');</script>";
    }
    $updateQuery->close();
}

// Fetch the current maintenance mode status
$query = $con->prepare("SELECT maintenance_mode FROM tblsettings LIMIT 1");
if ($query === false) {
    die("MySQL prepare statement error: " . $con->error);
}
$query->execute();
$query->bind_result($maintenance_mode);
$query->fetch();
$query->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Minell's Admin - CMS</title>
    <link rel="stylesheet" href="vendors/feather/feather.css">
    <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendors/select2/select2.min.css">
    <link rel="stylesheet" href="vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
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

                        <div class="col-6 grid-margin stretch-card">
                            <div class="card mt-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Scheduled Days Off</h5>
                            </div>
                                <div class="card-body">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#dayOffModal">
                                        Add Day Off
                                    </button>
                                    <table id="dayOffTable" class="display expandable-table" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Title</th>
                                                <th>Date</th>
                                                <th>Actions</th> <!-- Updated Actions Column -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Fetch scheduled days off from the database
                                            $result = $con->query("SELECT id, title, start_date FROM tbldayoffs"); // Exclude 'end_date'
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>
                                                        <td>{$row['title']}</td>
                                                        <td>{$row['start_date']}</td>
                                                        <td>
                                                            <button class='btn btn-warning edit-btn' data-id='{$row['id']}' data-title='{$row['title']}' data-start='{$row['start_date']}' data-toggle='modal' data-target='#editDayOffModal'>Edit</button>
                                                        </td>
                                                    </tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-3 grid-margin stretch-card" style="max-height: 300px; overflow-y: auto;">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Update Max Appointments Per Day</h5>
                                    <form method="POST" action="">
                                        <div class="form-group">
                                            <label for="maxAppointmentsPerDay">Max Appointments Per Day</label>
                                            <input type="number" class="form-control" id="maxAppointmentsPerDay" name="maxAppointmentsPerDay" value="<?php echo $maxAppointmentsPerDay; ?>" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Card for maintenance mode toggle -->
                        <div class="col-3 grid-margin stretch-card" style="max-height: 100px; overflow-y: auto;">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Maintenance Mode</h5>
                                    <div class="form-group">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="maintenanceSwitch" 
                                                <?php echo ($maintenance_mode == 1) ? 'checked' : ''; ?>>
                                            <label class="custom-control-label" for="maintenanceSwitch"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Day Off Scheduling Modal -->
                <div class="modal fade" id="dayOffModal" tabindex="-1" role="dialog" aria-labelledby="dayOffModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="dayOffModalLabel">Schedule Day Off</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="dayOffScheduleForm" method="POST" action="">
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <label for="dayOffTitle">Title</label>
                                            <input type="text" name="dayOffTitle" class="form-control" id="dayOffTitle" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="startDate">Date</label>
                                            <input type="date" name="startDate" class="form-control" id="startDate" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Schedule Day Off</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Edit Day Off Modal -->
                <div class="modal fade" id="editDayOffModal" tabindex="-1" role="dialog" aria-labelledby="editDayOffModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editDayOffModalLabel">Edit Day Off</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="editDayOffForm" method="POST" action="">
                                    <input type="hidden" id="editId" name="id">
                                    <div class="form-group">
                                        <label for="editTitle">Title</label>
                                        <input type="text" class="form-control" id="editTitle" name="title" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="editStartDate">Date</label>
                                        <input type="date" class="form-control" id="editStartDate" name="start_date" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <?php include_once('partials/_footer.php'); ?>
            </div>
        </div>
    </div>

    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.js"></script>
    <script>
        $(document).ready(function() {
            $('#dayOffTable').DataTable();

            // Populate the edit modal with the selected row's data
            $('.edit-btn').on('click', function() {
                var id = $(this).data('id');
                var title = $(this).data('title');
                var start = $(this).data('start');
                

                $('#editId').val(id);
                $('#editTitle').val(title);
                $('#editStartDate').val(start);
                
            });

            // Handling the maintenance mode toggle
            document.getElementById('maintenanceSwitch').addEventListener('change', function () {
                const status = this.checked ? 1 : 0;
                console.log("Maintenance Mode:", status);

                // AJAX request to update maintenance mode
                fetch('offday.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ maintenanceMode: status })
                })
                .then(response => response.text())
                .then(data => {
                    console.log("Maintenance mode response:", data);
                    alert("Maintenance mode has been " + (status ? "enabled." : "disabled."));
                })
                .catch(error => console.error('Error:', error));
            });
        });

        // Handling the schedule day off form submission
        document.getElementById('dayOffScheduleForm').addEventListener('submit', function(event) {
            event.preventDefault();
            
            const title = document.getElementById('dayOffTitle').value;
            const startDate = document.getElementById('startDate').value;

            console.log('Scheduled Day Off:', { title, startDate });

            // AJAX request to insert day off data
            fetch('offday.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    dayOffTitle: title,
                    startDate: startDate
                })
            })
            .then(response => response.text())
            .then(data => {
                console.log("Server Response:", data);
                
                // Display success alert
                alert("Day Off Scheduled successfully!");

                // Close the modal
                $('#dayOffModal').modal('hide');
                
                // Reload the page to reflect the new day off entry
                window.location.reload();
            })
            .catch(error => console.error('Error:', error));
        });

    </script>

    <script>
    document.getElementById('maintenanceSwitch').addEventListener('change', function () {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '', true); // Send the request to the current file
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log("Response: " + xhr.responseText);
            }
        };
        xhr.send('status=' + (this.checked ? 1 : 0));
    });
    </script>


    <?php include_once('partials/logout.php');?>
</body>
</html>
