<?php
session_start();
error_reporting(0);
include('partials/dbconnection.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Redirect if not logged in
if (strlen($_SESSION['salondbaid']) == 0) {
    header('location:login.php');
} else {
    // Fetch inquiries from the database, checking read/unread status
    $inquiriesQuery = mysqli_query($con, "SELECT * FROM tblinquiry ORDER BY submit_date DESC");

    // Handle the reply form submission
if (isset($_POST['submitReply'])) {
    $inquiryId = $_POST['inquiryId'];
    $reply = mysqli_real_escape_string($con, $_POST['reply']); 
    
    // Fetch the user_type for this inquiry
    $userTypeQuery = mysqli_query($con, "SELECT email, user_type FROM tblinquiry WHERE ID='$inquiryId'");
    $userRow = mysqli_fetch_assoc($userTypeQuery);
    $userEmail = $userRow['email'];
    $userType = $userRow['user_type'];

    // Insert the reply into the database
    $replyQuery = mysqli_query($con, "INSERT INTO tblreplies (inquiry_id, reply_text, reply_by, reply_date) VALUES ('$inquiryId', '$reply', 'ADMIN', NOW())");

    if ($replyQuery) {
        // Mark the inquiry as read after reply
        mysqli_query($con, "UPDATE tblinquiry SET status='read' WHERE ID='$inquiryId'");
        
        // If user_type is GUEST, send an email with the reply using PHPMailer
        if ($userType == 'GUEST') {
            // Load PHPMailer
            require 'vendor/autoload.php'; 

            $mail = new PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; 
                $mail->SMTPAuth = true;
                $mail->Username = 'kawamatsumachi@gmail.com'; 
                $mail->Password = 'hnlnepjapbvbsadw'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('kawamatsumachi@gmail.com', 'Minnel\'s Salon');
                $mail->addAddress($userEmail); // Recipient's email (Guest's email)

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Your Inquiry Reply';
                $mail->Body = "<strong>Reply:</strong> $reply";

                // Send email
                $mail->send();
                echo "<script>alert('Reply has been submitted and email sent to guest.');</script>";
            } catch (Exception $e) {
                echo "Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "<script>alert('Reply has been submitted.');</script>";
        }

        // Redirect back to inquiries page
        echo "<script>window.location.href = 'inquiries.php'</script>";
    } else {
        echo "<script>alert('Something went wrong. Error: " . mysqli_error($con) . "');</script>";
    }
}

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Client Inquiries</title>
    <link rel="stylesheet" href="vendors/feather/feather.css">
    <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- inject:css -->
    <link rel="stylesheet" href="css/vertical-layout-light/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="images/favicon.png" />

    <style>
        .inbox-table tr.unread {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .inbox-table tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }
        .inbox-table tr.read {
            color: #888;
        }
        .status-icon {
            font-size: 1.2rem;
        }
        .collapse-card {
            background-color: #ffffff;
            border: 1px solid #ddd;
            margin-top: 10px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .reply-button {
            margin-right: 10px;
        }
        .btn-modern {
            background-color: #007bff;
            color: white;
            border-radius: 50px;
            padding: 5px 20px;
            transition: background-color 0.3s ease;
        }
        .btn-modern:hover {
            background-color: #0056b3;
        }
        textarea.form-control {
            border-radius: 10px;
            resize: none;
        }
    </style>
</head>
<body>
<div class="container-scroller">
    <?php include_once('partials/_navbar.php'); ?>
    <div class="container-fluid page-body-wrapper">
        <?php include_once('partials/_sidebar.php'); ?>
        <div class="main-panel">
            <div class="content-wrapper">

                <!-- Modern Gmail-like Inquiries Inbox -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Client Inquiries</h4>
                    </div>
                    <div class="card-body">
                        <table class="table inbox-table">
                            <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>Sender</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>User Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($inquiryRow = mysqli_fetch_assoc($inquiriesQuery)) { 
                                    $status = $inquiryRow['status'] == 'read' ? 'read' : 'unread';
                                ?>
                                <tr class="<?php echo $status; ?>" data-bs-toggle="collapse" data-bs-target="#inquiry<?php echo $inquiryRow['ID']; ?>" aria-expanded="false" aria-controls="inquiry<?php echo $inquiryRow['ID']; ?>">
                                    <td>
                                        <?php if ($status == 'unread') { ?>
                                            <i class="fas fa-envelope status-icon text-primary"></i>
                                        <?php } else { ?>
                                            <i class="fas fa-envelope-open status-icon text-muted"></i>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo $inquiryRow['name']; ?></td>
                                    <td><?php echo $inquiryRow['subject']; ?></td>
                                    <td><?php echo date("F j, Y, g:i a", strtotime($inquiryRow['submit_date'])); ?></td>
                                    <td><?php echo $inquiryRow['user_type']; ?></td> <!-- Displaying user_type here -->
                                </tr>
                                <tr id="inquiry<?php echo $inquiryRow['ID']; ?>" class="collapse">
                                    <td colspan="4">
                                        <div class="collapse-card">
                                            <div><strong>Message:</strong> <?php echo $inquiryRow['message']; ?></div>
                                            <div class="mt-3">
                                                <form method="POST" action="inquiries.php">
                                                    <input type="hidden" name="inquiryId" value="<?php echo $inquiryRow['ID']; ?>">
                                                    <textarea class="form-control" name="reply" rows="3" placeholder="Write your reply here..." required></textarea>
                                                    <div class="mt-3">
                                                        <button type="submit" name="submitReply" class="btn btn-modern">Reply</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>

                                        <!-- Display Replies -->
                                        <?php
                                        $repliesQuery = mysqli_query($con, "SELECT * FROM tblreplies WHERE inquiry_id = '{$inquiryRow['ID']}' ORDER BY reply_date DESC");
                                        if (mysqli_num_rows($repliesQuery) > 0) {
                                            while ($replyRow = mysqli_fetch_assoc($repliesQuery)) {
                                        ?>
                                        <div class="card mt-3">
                                            <div class="card-body">
                                                <strong>Reply By:</strong> <?php echo $replyRow['reply_by']; ?><br>
                                                <strong>Reply:</strong> <?php echo $replyRow['reply_text']; ?><br>
                                                <small class="text-muted"><em><?php echo date("F j, Y, g:i a", strtotime($replyRow['reply_date'])); ?></em></small>
                                            </div>
                                        </div>
                                        <?php } } ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- End Modern Gmail-like Inquiries Inbox -->

            </div>
        </div>
    </div>
</div>

<!-- Required scripts -->
<!-- Load jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Load DataTables -->
<script src="https://cdn.datatables.net/2.1.7/js/jquery.dataTables.min.js"></script>

<!-- Load DataTables Bootstrap 5 integration -->
<script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.min.js"></script>

<!-- Bootstrap (must come after jQuery) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Other scripts -->
<script src="vendors/js/vendor.bundle.base.js"></script>
<script src="js/vertical-layout-light.js"></script>
<!-- inject:js -->
<script src="js/off-canvas.js"></script>
<script src="js/hoverable-collapse.js"></script>
<script src="js/template.js"></script>


<script>
function markAsRead(inquiryId) {
    $.ajax({
        url: 'inquiries.php',
        type: 'POST',
        data: { inquiryId: inquiryId },
        success: function(response) {
            console.log("AJAX success:", response);
            $('tr[data-bs-target="#inquiry' + inquiryId + '"]').removeClass('unread').addClass('read');
            $('tr[data-bs-target="#inquiry' + inquiryId + '"] .status-icon').removeClass('text-primary').addClass('text-muted');
        },
        error: function(xhr, status, error) {
            console.error("AJAX error:", status, error);
        }
    });
}
</script>
<?php include_once('partials/logout.php');?>

</body>
</html>
