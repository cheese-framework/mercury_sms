<?php

namespace App\Notifiable;

use App\Core\Helper;
use PHPMailer\PHPMailer\PHPMailer;


class Mail
{

    private PHPMailer $mail;
    private array $error = [];
    public $sent = false;

    public function __construct()
    {
        $this->mail = new PHPMailer(SHOW_ERROR_DETAIL);
        $this->mail->isSMTP();
        $this->mail->Host = "smtp.gmail.com";
        $this->mail->SMTPAuth = true;
        $this->mail->Username = SMTP_USERNAME;
        $this->mail->Password = SMTP_PASSWORD;
        $this->mail->SMTPSecure = 'tls';
        $this->mail->Port = SMTP_PORT;
    }

    public function sendMail(
        array $to,
        $subject,
        $message,
        $fromName,
        $from
    ) {
        $this->mail->From = $from;
        $this->mail->FromName = $fromName;

        foreach ($to as $key => $value) {
            $this->mail->addAddress($key, $value);
        }

        $this->mail->isHTML(true);
        $this->mail->addReplyTo($from, $fromName);
        $this->mail->Subject = $subject;
        $this->mail->Body = $message;
        $this->mail->AltBody = $message;
        try {
            $this->mail->send();
            $this->sent = TRUE;
        } catch (\Exception $e) {
            $this->error[] = $this->mail->ErrorInfo;
            if (SHOW_ERROR_DETAIL) {
                $this->error[] = $e->getMessage();
            }
            $this->sent = FALSE;
        }
    }


    public function sendMailWithAttachments(
        array $to,
        $subject,
        $message,
        $fromName,
        $from,
        array $attachment
    ) {
        $this->mail->From = $from;
        $this->mail->FromName = $fromName;

        // setup address

        foreach ($to as $key => $value) {
            $this->mail->addAddress($key, $value);
        }


        // add attachments   

        foreach ($attachment as $key => $value) {
            $this->mail->addAttachment($value, $key);
        }

        $this->mail->isHTML(true);
        $this->mail->addReplyTo($from, $fromName);
        $this->mail->Subject = $subject;
        $this->mail->Body = $message;
        $this->mail->AltBody = $message;
        try {
            $this->mail->send();
            $this->sent = TRUE;
        } catch (\Exception $e) {
            $this->error[] = $this->mail->ErrorInfo;
            if (SHOW_ERROR_DETAIL) {
                $this->error[] = $e->getMessage();
            }
            $this->sent = FALSE;
        }
    }

    public function returnError()
    {
        if (!empty($this->error)) {
            Helper::prettify($this->error);
        } else {
            return 0;
        }
    }
}
