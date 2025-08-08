<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');

// Redirect if not logged in
if (strlen($_SESSION['salondbaid']) == 0) {
    header('location:login.php');
} else {
    // Insert new message
    if (isset($_POST['submit'])) {
        $clientName = $_POST['clientName'];
        $clientEmail = $_POST['clientEmail'];
        $message = $_POST['message'];
        $status = 'New'; // Default status

        $query = mysqli_query($con, "INSERT INTO tblmessages (ClientName, ClientEmail, Message, Status) VALUES ('$clientName', '$clientEmail', '$message', '$status')");
        if ($query) {
            echo "<script>alert('Message has been added.');</script>";
            echo "<script>window.location.href = 'client_inbox.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }

    // Update message status
    if (isset($_POST['updateMessage'])) {
        $messageId = $_POST['messageId'];
        $status = $_POST['status'];

        $query = mysqli_query($con, "UPDATE tblmessages SET Status='$status' WHERE ID='$messageId'");
        if ($query) {
            echo "<script>alert('Message status has been updated.');</script>";
            echo "<script>window.location.href = 'client_inbox.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }

    // Delete message
    if (isset($_GET['delid'])) {
        $delid = intval($_GET['delid']);
        $query = mysqli_query($con, "DELETE FROM tblmessages WHERE ID='$delid'");
        if ($query) {
            echo "<script>alert('Message has been deleted.');</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }

    // Fetch messages for the table
    $messagesQuery = mysqli_query($con, "SELECT * FROM tblmessages ORDER BY DateReceived DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Client Inbox</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="vendors/feather/feather.css">
    <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <!-- inject:css -->
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <link rel="shortcut icon" href="images/favicon.png" />
    <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.bootstrap5.css">
</head>
<body>
<div class="container-scroller">
    <?php include_once('partials/_navbar.php'); ?>
    <div class="container-fluid page-body-wrapper">
        
        <?php include_once('partials/_sidebar.php'); ?>

        <div class="main-panel">
            <div class="content-wrapper">

                <!-- Modal for Adding Messages -->
                <div class="modal fade" id="addMessageModal" tabindex="-1" role="dialog" aria-labelledby="addMessageModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addMessageModalLabel">Add Message</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="client_inbox.php">
                                    <div class="form-group">
                                        <label for="clientName">Client Name</label>
                                        <input type="text" class="form-control" id="clientName" name="clientName" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="clientEmail">Client Email</label>
                                        <input type="email" class="form-control" id="clientEmail" name="clientEmail" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="message">Message</label>
                                        <textarea class="form-control" id="message" name="message" required></textarea>
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-primary mr-2">Submit</button>
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal for Editing Messages -->
                <div class="modal fade" id="editMessageModal" tabindex="-1" role="dialog" aria-labelledby="editMessageModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editMessageModalLabel">Edit Message Status</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="client_inbox.php">
                                    <input type="hidden" id="editMessageId" name="messageId">
                                    <div class="form-group">
                                        <label for="editStatus">Status</label>
                                        <select id="editStatus" name="status" class="form-control" required>
                                            <option value="New">New</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Resolved">Resolved</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="updateMessage" class="btn btn-primary mr-2">Update</button>
                                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message Table -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Client Messages</h4>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addMessageModal">Add Message</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="example" class="display expandable-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Message</th>
                                        <th>Date Received</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($messagesQuery)) { ?>
                                        <tr>
                                            <td><?php echo $row['ID']; ?></td>
                                            <td><?php echo $row['ClientName']; ?></td>
                                            <td><?php echo $row['ClientEmail']; ?></td>
                                            <td><?php echo $row['Message']; ?></td>
                                            <td><?php echo $row['DateReceived']; ?></td>
                                            <td>
                                                <div class="badge badge-<?php echo strtolower($row['Status']) === 'resolved' ? 'success' : (strtolower($row['Status']) === 'in progress' ? 'warning' : 'secondary'); ?>">
                                                    <?php echo $row['Status']; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editMessageModal"
                                                    onclick="editMessage('<?php echo $row['ID']; ?>', '<?php echo $row['Status']; ?>')">Edit Status</button>
                                                <a href="client_inbox.php?delid=<?php echo $row['ID']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this message?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End Table -->

            </div>
        </div>
    </div>
</div>

<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.1.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable();
    });

    function editMessage(id, status) {
        $('#editMessageId').val(id);
        $('#editStatus').val(status);
    }
</script>
</body>
</html>
