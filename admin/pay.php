<?php

use App\Helper\Voucher\Voucher;
use App\Notifiable\Mailable;
use App\Notifiable\Notifiable;

include_once "../init.php";

if (isset($_GET['data']) && $_GET['data'] != "") {
    $data = json_decode($_GET['data'], true);
    $amount = $data['amountToPay'];
    $usingSMS = $data['usingSMS'] == "TRUE" ? TRUE : FALSE;
    $email = $data['email'];
    $phone = $data['phone'];
    $duration = $data['duration'];
    $type = $data['type'];
    $date = $data['date'];

    try {
        $voucher = Voucher::addVoucher($type, $date, $amount, $usingSMS);
        if ($phone) {
            $phone = "+" . trim($phone);
            try {
                $notify = new Notifiable(['twilio']);
                $notify->sendNotificationViaTwilio($phone, $voucher);
            } catch (\Throwable $th) {
                echo "ERROR: " . $th->getMessage() . "\n";
            }
        }

        $data = [
            'to' => $email,
            'name' => $email,
            'from' => DEFAULT_FROM,
            'fromName' => DEFAULT_FULLNAME,
            'subject' => 'Subscription Activation Payment',
            'amountToPay' => $amount,
            'email' => $email,
            'phone' => $phone,
            'type' => $type,
            'sms' => $usingSMS,
            'duration' => $duration,
            'date' => $date,
            'voucher' => $voucher
        ];

        // Send email via mailable
        $mail = new Mailable('activated', $data);
        $sent = $mail->build()->send();
        if ($sent) {
            echo "OK";
        } else {
            echo "Could not send email\n";
            Voucher::delete($voucher);
        }
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage();
    }
} else {
    echo "FAILED\n";
}
