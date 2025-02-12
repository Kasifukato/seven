<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure Composer autoload is included

function sendEmail($recipientEmail, $recipientName, $productName, $productQuantity, $saleFormUrl)
{
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to Gmail
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'aasamshaparajuli935@gmail.com';                    // Your Gmail address from environment variables
        $mail->Password   = 'omrz ekbv yztl kxnv';                    // Your Gmail app password from environment variables
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
        $mail->Port       = 587;                                    // TCP port for TLS

        // Recipients
        $mail->setFrom('aasamshaparajuli935@gmail.com', 'Vault Vision');    // Sender's email
        $mail->addAddress($recipientEmail, $recipientName);         // Add recipient

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = "Request for Product: $productName";
        $mail->Body    = "<p>Dear $recipientName,</p>
                        <p>I hope this email finds you well. We would like to request the following product from your inventory:</p>
                        <ul>
                            <li><strong>Product Name:</strong> $productName</li>
                            <li><strong>Quantity:</strong> $productQuantity</li>
                        </ul>
                        <p>Please confirm the availability of the product.</p>
                        <p>Thank you for your cooperation.</p>
                        <p>Best regards,</p>
                        <p>Inventory System</p>";

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>