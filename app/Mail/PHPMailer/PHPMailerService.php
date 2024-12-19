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
        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host = $generalSetting->smtp_host;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $generalSetting->smtp_username;
        $this->mail->Password = $generalSetting->smtp_password;
        $this->mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls'); // or 'ssl'
        $this->mail->Port = $generalSetting->smtp_port;

        // Default sender
        $this->mail->setFrom($generalSetting->from_email, $generalSetting->from_name);
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
