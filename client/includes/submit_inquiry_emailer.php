<?php
// Include database connection
include_once 'dbconnection.php';

// Load PHPMailer files
require 'vendor/autoload.php'; // Adjust the path to your autoload.php for PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $subject = mysqli_real_escape_string($con, $_POST['subject']);
    $message = mysqli_real_escape_string($con, $_POST['message']);

    // Insert the inquiry into the database
    $query = "INSERT INTO tblinquiry (name, email, subject, message, submit_date, user_type) 
              VALUES ('$name', '$email', '$subject', '$message', NOW(), 'GUEST')";
    $result = mysqli_query($con, $query);

    if ($result) {
        // Send email to admin using PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'kawamatsumachi@gmail.com'; // SMTP username
            $mail->Password   = 'hnlnepjapbvbsadw'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom($email, $name);
            $mail->addAddress('kawamatsumachi@gmail.com', 'Minnel\'s Salon'); // Admin's email address

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = '
                <div style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 40px; color: #333;">
                    <div style="max-width: 600px; margin: auto; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                        <div style="text-align: center; margin-bottom: 30px;">
                            <h2 style="color: #FFDF00; margin-bottom: 0;">Minnel\'s Salon</h2>
                            <p style="color: #666; font-size: 14px;">Beauty & Style at Your Fingertips</p>
                        </div>
                        <div style="padding: 20px; background: white; border-radius: 0 0 8px 8px; line-height: 1.6;">
                            <p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>
                            <p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>
                            <p><strong>Message:</strong></p>
                            <p>' . nl2br(htmlspecialchars($message)) . '</p>
                        </div>
                        <div style="text-align: center; padding: 10px; font-size: 0.9em; color: #777; background-color: #f4f4f4; border-radius: 0 0 8px 8px;">
                            &copy; ' . date("Y") . ' Minnel\'s Salon. All rights reserved.
                        </div>
                    </div>
                </div>
            ';

            // Send email to admin
            $mail->send();

            // Send acknowledgment email to the user
            $ackMail = new PHPMailer(true);

            // Server settings for acknowledgment
            $ackMail->isSMTP();
            $ackMail->Host       = 'smtp.gmail.com';
            $ackMail->SMTPAuth   = true;
            $ackMail->Username   = 'kawamatsumachi@gmail.com';
            $ackMail->Password   = 'hnlnepjapbvbsadw';
            $ackMail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $ackMail->Port       = 587;

            // Recipients for acknowledgment
            $ackMail->setFrom('no-reply@minnels-salon.com', 'Minnel\'s Salon'); // No-reply email address
            $ackMail->addAddress($email, $name); // User's email address

            // Content for acknowledgment
            $ackMail->isHTML(true);
            $ackMail->Subject = 'Thank You for Contacting Minnel\'s Salon';
            $ackMail->Body = '
                <div style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 40px; color: #333;">
                    <div style="max-width: 600px; margin: auto; background-color: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);">
                        <div style="text-align: center; margin-bottom: 30px;">
                            <h2 style="color: #FFDF00; margin-bottom: 0;">Minnel\'s Salon</h2>
                            <p style="color: #666; font-size: 14px;">Beauty & Style at Your Fingertips</p>
                        </div>
                        <div style="padding: 20px; background: white; border-radius: 0 0 8px 8px; line-height: 1.6;">
                            <p style="margin: 0;">Hello <strong>' . htmlspecialchars($name) . '</strong>,</p>
                            <p>Thank you for reaching out to us.</p>
                            <p>We have received your message and will get back to you as soon as possible.</p>
                            <p>- Minnel\'s Salon Team</p>
                        </div>
                        <div style="text-align: center; padding: 10px; font-size: 0.9em; color: #777; background-color: #f4f4f4; border-radius: 0 0 8px 8px;">
                            &copy; ' . date("Y") . ' Minnel\'s Salon. All rights reserved.
                        </div>
                    </div>
                </div>
            ';

            // Send acknowledgment email
            $ackMail->send();

            // Redirect to success page or show success message
            echo "<script>
                alert('Inquiry submitted successfully!');
                window.location.href='/index.php'; // Redirect based on login status
            </script>";
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Failed to submit inquiry.";
    }
}
?>

