<?php
require 'phpmailer/PHPMailer.php';
require 'phpmailer/Exception.php';
require 'phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Validate input
$errors = [];
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

if (!$name) $errors['name'] = 'Please enter your name';
if (!$email) $errors['email'] = 'Please enter a valid email address';
if (!$subject) $errors['subject'] = 'Please enter a subject';
if (!$message) $errors['message'] = 'Please enter your message';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Create PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Server settings (using your credentials)
    $mail->isSMTP();
    $mail->Host = 'mail.fahim-khan-96.org';
    $mail->SMTPAuth = true;
    $mail->Username = 'my_portfolio@fahim-khan-96.org';
    $mail->Password = '=;4L@UEs!OYd';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('my_portfolio@fahim-khan-96.org', 'Portfolio Contact');
    $mail->addAddress('enamul12412@gmail.com', 'Enamul Hakim Khan'); // Your receiving email
    $mail->addReplyTo($email, $name);

    // Content
    $mail->isHTML(true);
    $mail->Subject = "Portfolio Contact: $subject";
    $mail->Body = "
        <h2>New Contact Form Submission</h2>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Subject:</strong> $subject</p>
        <p><strong>Message:</strong></p>
        <p>$message</p>
    ";
    $mail->AltBody = "Name: $name\nEmail: $email\nSubject: $subject\nMessage:\n$message";

    // Send to you
    $mail->send();
    
    // Send confirmation to sender
    $mail->clearAddresses();
    $mail->addAddress($email, $name);
    $mail->Subject = "Thank you for contacting me";
    $mail->Body = "
        <h2>Thank You, $name!</h2>
        <p>I've received your message regarding \"$subject\" and will get back to you soon.</p>
        <p>Your message:</p>
        <blockquote>$message</blockquote>
        <p>Best regards,<br>Enamul Hakim Khan</p>
    ";
    $mail->AltBody = "Thank you $name!\n\nI've received your message about \"$subject\".\n\nYour message:\n$message\n\nBest regards,\nEnamul Hakim Khan";

    $mail->send();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => "Message could not be sent. Error: {$mail->ErrorInfo}"]);
}
?>