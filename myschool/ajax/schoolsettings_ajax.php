<?php

use App\Core\Helper;
use App\Database\Database;

include_once "../../init.php";


if (isset($_GET['type'])) {
    $type = $_GET['type'];
    $value = $_GET['value'];
    $value = ($value == "true") ? 1 : 0;
    $school = $_GET['school'];
    $conn = Database::getInstance();
    if ($type == "sms_change") {
        $stmt = $conn->dbh->prepare("UPDATE school SET useSMS=:use_sms WHERE schoolId=:school_id");
        $updated = $stmt->execute([':use_sms' => $value, ':school_id' => $school]);
        if ($updated) {
            echo "OK";
        } else {
            echo "Something went wrong";
        }
    } else if ($type == "assess_change") {
        $val = $_GET['value'];
        $stmt = $conn->dbh->prepare("UPDATE school SET allowOnlineAssessment = :allowed WHERE schoolId=:school");
        if ($val == "true") {
            if (Helper::getSchoolType($school) > 1) {
                $updated = $stmt->execute([':allowed' => $value, ':school' => $school]);
                if ($updated) {
                    echo "OK";
                } else {
                    echo "Something went wrong";
                }
            } else {
                echo "Your current type of school does not support online assessment";
            }
        } else {
            $updated = $stmt->execute([':allowed' => $value, ':school' => $school]);
            if ($updated) {
                echo "OK";
            } else {
                echo "Something went wrong";
            }
        }
    } else if ($type == "school_change") {
        $stmt = $conn->dbh->prepare("UPDATE school SET schoolType=:schooltype WHERE schoolId=:school");
        $value = $_GET['value'];
        if ($value != "") {
            $updated = $stmt->execute([':schooltype' => $value, ':school' => $school]);
            if ($updated) {
                echo "OK";
            } else {
                echo "Something went wrong";
            }
        } else {
            echo "Something went wrong";
        }
    } else if ($type == "currency_change") {
        $value = $_GET['value'];
        $stmt = $conn->dbh->prepare("UPDATE school SET money_sign = :money_sign WHERE schoolId=:school");
        $updated = $stmt->execute([':money_sign' => $value, ':school' => $school]);
        if ($updated) {
            echo "OK";
        } else {
            echo "Something went wrong";
        }
    }
}
