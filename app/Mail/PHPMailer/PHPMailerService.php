<?php

namespace App\Mail\PHPMailer;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Models\GeneralSetting;
class PHPMailerService
{
    protected $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $generalSetting             = GeneralSetting::find('1');
        echo '<pre>';print_r($generalSetting);die;
        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host = env('MAIL_HOST', 'smtp-relay.brevo.com');
        $this->mail->SMTPAuth = true;
        $this->mail->Username = env('MAIL_USERNAME', '819808001@smtp-brevo.com');
        $this->mail->Password = env('MAIL_PASSWORD', 'kTA9qQXYSC4281MD');
        $this->mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls'); // or 'ssl'
        $this->mail->Port = env('MAIL_PORT', 587);

        // Default sender
        $this->mail->setFrom(env('MAIL_FROM_ADDRESS', 'no-reply@macoline.in'), env('MAIL_FROM_NAME', 'Macoline Web Application'));
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
            // return 'Mail has been sent';
            return true;
        } catch (Exception $e) {
            // return 'Mail could not be sent. Error: ' . $this->mail->ErrorInfo;
            return false;
        }
    }
}
