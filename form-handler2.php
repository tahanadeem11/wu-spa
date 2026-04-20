<?php
/**
 * File Name: form-handler2.php
 * Process contact form (with phone) and send email using PHPMailer via SMTP
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
    $phone = filter_var($_POST['phone'], FILTER_SANITIZE_STRING);
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
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Recipients
        $mail->setFrom('wu-spa@getmeonlocal.com', 'Wu Spa Website');
        $mail->addAddress($to_email, 'Wu Spa Admin');
        $mail->addReplyTo($from_email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Form Submission (Home Page): ' . $name;
        
        $email_body = "<h3>New Message from Wu Spa Website (Handled by Form 2)</h3>";
        $email_body .= "<p><strong>Name:</strong> " . $name . "</p>";
        $email_body .= "<p><strong>Email:</strong> " . $from_email . "</p>";
        $email_body .= "<p><strong>Phone:</strong> " . $phone . "</p>";
        $email_body .= "<p><strong>Message:</strong><br/>" . nl2br($message) . "</p>";
        
        $mail->Body = $email_body;
        $mail->AltBody = "Name: $name\nEmail: $from_email\nPhone: $phone\n\nMessage:\n$message";

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
