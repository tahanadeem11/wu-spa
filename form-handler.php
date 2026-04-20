<?php
/**
 * File Name: form-handler.php
 * Process contact form and send email using PHPMailer via SMTP
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'plugins/phpmailer/Exception.php';
require 'plugins/phpmailer/PHPMailer.php';
require 'plugins/phpmailer/SMTP.php';

// Check if action is set (added by custom.js)
if (isset($_POST['action']) && $_POST['action'] == 'contactform'):

    // Sanitize input data
    $name = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $from_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);

    $to_email = "excellentwuwuhands@wu-spa.com"; // Primary recipient
    
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'getmeonlocal.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'wu-spa@getmeonlocal.com';
        $mail->Password   = 'PASScode123@#';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Port 465 usually uses SMTPS/SSL
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('wu-spa@getmeonlocal.com', 'Wu Spa Website'); // Authenticated user as sender
        $mail->addAddress($to_email, 'Wu Spa Admin');
        $mail->addReplyTo($from_email, $name); // Reply to the person who filled the form

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Contact Form Submission: ' . $name;
        
        $email_body = "<h3>New Message from Wu Spa Contact Form</h3>";
        $email_body .= "<p><strong>Name:</strong> " . $name . "</p>";
        $email_body .= "<p><strong>Email:</strong> " . $from_email . "</p>";
        $email_body .= "<p><strong>Message:</strong><br/>" . nl2br($message) . "</p>";
        
        $mail->Body = $email_body;
        $mail->AltBody = "Name: $name\nEmail: $from_email\n\nMessage:\n$message";

        $mail->send();
        
        echo json_encode(array(
            'success' => true,
            'message' => "Message Sent Successfully!"
        ));

    } catch (Exception $e) {
        echo json_encode(array(
            'success' => false,
            'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"
        ));
    }

else:

    echo json_encode(array(
        'success' => false,
        'message' => "Invalid Request!"
    ));

endif;
die;
