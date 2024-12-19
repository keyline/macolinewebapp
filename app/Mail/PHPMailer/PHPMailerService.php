<?php

namespace App\Mail\PHPMailer;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerService
{
    protected $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host = env('MAIL_HOST', 'smtp.example.com');
        $this->mail->SMTPAuth = true;
        $this->mail->Username = env('MAIL_USERNAME', 'your_username');
        $this->mail->Password = env('MAIL_PASSWORD', 'your_password');
        $this->mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls'); // or 'ssl'
        $this->mail->Port = env('MAIL_PORT', 587);

        // Default sender
        $this->mail->setFrom(env('MAIL_FROM_ADDRESS', 'noreply@example.com'), env('MAIL_FROM_NAME', 'Your Application'));
    }

    public function sendMail($to, $subject, $body)
    {
        try {
            // Recipient
            $this->mail->addAddress($to);

            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;

            // Send email
            $this->mail->send();
            return 'Mail has been sent';
        } catch (Exception $e) {
            return 'Mail could not be sent. Error: ' . $this->mail->ErrorInfo;
        }
    }
}
