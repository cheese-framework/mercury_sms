<?php

use App\Core\Helper;
use App\School\SMSParent;
use App\Helper\Logger;
use App\Notifiable\Components\Components;
use App\Notifiable\Mail;
use App\Notifiable\Notifiable;
use App\Queue\QueuePublisher;

include "../init.php";
date_default_timezone_set("Africa/Banjul");

if (isset($_GET['class'])) {
    $class = $_GET['class'];
    $year = $_GET['year'];
    $term = $_GET['term'];
    $school = $_GET['school'];
    $debtors = Helper::getDebtors($class, $year, $term, $school);

    if ($debtors == null) {
        echo "No debtors found!";
    } else {
        $to = [];
        $parentIds = [];
        foreach ($debtors as $debtor) {
            $paremail = SMSParent::getParentEmail($debtor);
            if ($paremail != "") {
                $id = SMSParent::getParentIdByEmail($paremail);
                if ($id != "") {
                    $parentIds[] = $id;
                }
                $to[] = $paremail;
            }
        }
        $to = array_unique($to);
        $parentIds = array_unique($parentIds);
        $recepients = [];
        $subject = "Arrears Notification From " . Helper::getSchoolName($school);
        $msg =
            Components::header("Arrears Notification - Tuition Fee Payment is Due", "h4", "center") . Components::body(
                "<br>Hello, we are writing to you in regards of your ward's tuition fee.

        It is now due and we would be so happy if you could pay soon in order to avoid us from putting a hold on your ward's learning.

        We do not take pleasure in sending students home due to tuition fees arrears, but please understand that we would have no other choice than to send your ward(s) home if arrears are not settled

        The Management & Administrator<br>
        <small>" . Helper::getSchoolName($school) . "</small><br>
        <small><i>Powered by Mercury School Management System</i><small>"

            );

        try {
            $mail = new Mail();
            // $receivers = [];
            foreach ($to as $t) {
                $receivers[] = [$t => $t];
            }
            $mail->sendBulkMail($receivers, $subject, $msg, Helper::getSchoolName($school), Helper::getSchoolEmail($school));

            // send sms notification

            if (Helper::isUsingSMS($school)) {
                foreach ($parentIds as $parId) {
                    $not = new Notifiable(['twilio'], $parId);
                    $not->notify("You have mail from " . Helper::getSchoolName($school) . ".");
                }
            }

            echo "OK";
            Helper::appendNoticeDate($class, $school);
            $log = new Logger($msg, "Arrears Notification sent for " . Helper::classEncode($class), $school, 1);
            $log::save();
        } catch (Exception $e) {
            echo "Could not send notification";
        }
    }
}
