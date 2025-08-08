<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');

// Redirect if not logged in
if (strlen($_SESSION['salondbaid']) == 0) {
    header('location:login.php');
} else {
    // Insert new FAQ
    if (isset($_POST['submit'])) {
        $question = $_POST['question'];
        $answer = $_POST['answer'];

        $query = mysqli_query($con, "INSERT INTO tblfaqs (question, answer) VALUES ('$question', '$answer')");
        
        if ($query) {
            echo "<script>alert('FAQ has been added.');</script>";
            echo "<script>window.location.href = 'faqs.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }

    // Update FAQ
    if (isset($_POST['updateFaq'])) {
        $faqid = $_POST['faqid'];
        $question = $_POST['question'];
        $answer = $_POST['answer'];

        $query = mysqli_query($con, "UPDATE tblfaqs SET question='$question', answer='$answer' WHERE id='$faqid'");
        
        if ($query) {
            echo "<script>alert('FAQ has been updated.');</script>";
            echo "<script>window.location.href = 'faqs.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }

    // Delete FAQ
    if (isset($_GET['delid'])) {
        $delid = $_GET['delid'];

        $query = mysqli_query($con, "DELETE FROM tblfaqs WHERE id='$delid'");
        
        if ($query) {
            echo "<script>alert('FAQ has been deleted.');</script>";
            echo "<script>window.location.href = 'faqs.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }


    // Fetch all FAQs
    $faqQuery = mysqli_query($con, "SELECT * FROM tblfaqs");
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

                    <!-- Modal for Adding FAQs -->
                    <div class="modal fade" id="addFaqModal" tabindex="-1" role="dialog" aria-labelledby="addFaqModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addFaqModalLabel">Add FAQ</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="faqs.php">
                                        <div class="form-group">
                                            <label for="faqQuestion">Question</label>
                                            <input type="text" class="form-control" id="faqQuestion" name="question" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="faqAnswer">Answer</label>
                                            <textarea class="form-control" id="faqAnswer" name="answer" rows="4" required></textarea>
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-primary mr-2">Submit</button>
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Modal for Editing FAQ -->
                    <div class="modal fade" id="editFaqModal" tabindex="-1" role="dialog" aria-labelledby="editFaqModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editFaqModalLabel">Edit FAQ</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="faqs.php">
                                        <!-- Hidden field to store FAQ ID -->
                                        <input type="hidden" id="editFaqId" name="faqid">
                                        
                                        <div class="form-group">
                                            <label for="editFaqQuestion">Question</label>
                                            <input type="text" class="form-control" id="editFaqQuestion" name="question" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="editFaqAnswer">Answer</label>
                                            <textarea class="form-control" id="editFaqAnswer" name="answer" rows="4" required></textarea>
                                        </div>
                                        
                                        <button type="submit" name="updateFaq" class="btn btn-primary">Update</button>
                                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FAQs Table -->
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>FAQs List</h4>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addFaqModal">Add FAQ</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example" class="display expandable-table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Question</th>
                                            <th>Answer</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $ret = mysqli_query($con, "SELECT * FROM tblfaqs");
                                        $cnt = 1;
                                        while ($row = mysqli_fetch_array($ret)) {
                                        ?>
                                        <tr>
                                            <th scope="row"><?php echo $cnt; ?></th>
                                            <td><?php echo $row['question']; ?></td>
                                            <td><?php echo substr($row['answer'], 0, 50) . '...'; // Limit answer preview ?></td>
                                            <td>Created: <?php echo $row['created_at']; ?> <br>
                                            Updated: <?php echo $row['updated_at']; ?></td>
                                            <td>
                                                <button class="btn btn-warning btn-sm" 
                                                    onclick="editFaq('<?php echo $row['id']; ?>', '<?php echo addslashes($row['question']); ?>', '<?php echo addslashes($row['answer']); ?>')">
                                                    Edit
                                                </button>
                                                <button class="btn btn-danger btn-sm" onclick="deleteFaq('<?php echo $row['id']; ?>')">Delete</button>
                                            </td>
                                        </tr>
                                        <?php
                                        $cnt++;
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- End FAQs Table -->

                </div>
                <?php include_once('partials/_footer.php'); ?>
            </div>
        </div>
    </div>
    <!-- container-scroller -->

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.js"></script>

    <script src="vendors/js/vendor.bundle.base.js"></script>
    <script src="vendors/typeahead.js/typeahead.bundle.min.js"></script>
    <script src="vendors/select2/select2.min.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/template.js"></script>
    <script src="js/settings.js"></script>
    <script src="js/todolist.js"></script>
    <script src="js/file-upload.js"></script>
    <script src="js/typeahead.js"></script>
    <script src="js/select2.js"></script>

    <script>
        $(document).ready(function() {
        new DataTable('#example');
    });

    // Edit FAQ Function 
    function editFaq(id, question, answer) {
        // Set values in modal
        $('#editFaqId').val(id);
        $('#editFaqQuestion').val(question);
        $('#editFaqAnswer').val(answer);

        // Show edit modal
        $('#editFaqModal').modal('show');
    }

    // Delete FAQ confirmation
    function deleteFaq(id) {
        if (confirm('Are you sure you want to delete this FAQ?')) {
            window.location.href = 'faqs.php?delid=' + id;
        }
    }

    </script>
        <!-- Logout Confirmation Modal -->
        <?php include_once('partials/logout.php');?>

</body>

</html>
