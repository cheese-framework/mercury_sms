<?php

use App\Core\Helper;
use App\Notifiable\Mail;
use App\Helper\Logger;
use App\Notifiable\Components\Components;
use App\Notifiable\Mailable;

include "./includes/header.php";

if (Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}


if (isset($_GET['success']) && $_GET['success'] == 'true') {
    $token = $_GET['token'];
    $agreement = new \PayPal\Api\Agreement();
    try {
        // Execute agreement
        $agreement->execute($token, $apiContext);
    } catch (PayPal\Exception\PayPalConnectionException $ex) {
        exit(1);
    } catch (Exception $ex) {
        exit(1);
    }

    $details = $agreement->getAgreementDetails();
    $payer = $agreement->getPayer();
    $payerInfo = $payer->getPayerInfo();
    $plan = $agreement->getPlan();
    $payment = $plan->getPaymentDefinitions()[0];

    $paymentId = $agreement->getId();
    $state = $agreement->getState();
    $startDate = $agreement->getStartDate();
    $frequency = $payment->getFrequency();
    $amount = $payment->getAmount();
    $currency = $amount->getCurrency();
    $amount = $amount->getValue();
    $cycles = $payment->getCycles();
    $charge = $payment->getChargeModels();
    $tax = $charge[0]->getAmount()->getValue();
    $interval = $payment->getFrequencyInterval();
    $payer = $agreement->getPayer()->getPayerInfo();
    $payerEmail = $payer->getEmail();
    $payUsername = $payer->getFirstName() . " " . $payer->getLastName();
    $payerId = $payer->getPayerId();


    $paymentExecutedID = Helper::addBillingInfo($paymentId, $frequency, $currency, $amount, $cycles, $tax, $interval, $state, $payerEmail, $payUsername, $payerId, $startDate);
    if ($paymentExecutedID != false) {
        if (Helper::activateSchool($schoolId, $paymentExecutedID)) {
            $log = new Logger("Your subscription has been activated", "Subscription State Check with Paypal", $schoolId, 1);
            $log::save();
            $temp = strtotime($startDate);
            $date = date("dS F, Y", $temp);
            // Send statement to email
            $messageData = [
                'to' => Helper::getSchoolEmail($schoolId),
                'name' => Helper::getSchoolName($schoolId),
                'from' => DEFAULT_FROM,
                'fromName' => DEFAULT_FULLNAME,
                'subject' => 'Mini Statement for Subscription',
                'paymentId' => $paymentId,
                'state' => $state,
                'date' => $date,
                'frequency' => $frequency,
                'amount' => $amount,
                'tax' => $tax,
                'cycles' => $cycles,
                'interval' => $interval,
                'payerId' => $payerId
            ];

            $secondData = [
                'to' => $payerEmail,
                'name' =>  $payUsername,
                'from' => DEFAULT_FROM,
                'fromName' => DEFAULT_FULLNAME,
                'subject' => 'Mini Statement for Subscription',
                'paymentId' => $paymentId,
                'state' => $state,
                'date' => $date,
                'frequency' => $frequency,
                'amount' => $amount,
                'tax' => $tax,
                'cycles' => $cycles,
                'interval' => $interval,
                'payerId' => $payerId
            ];

            $mail = new Mailable('activated', $messageData);
            $mail2 = new Mailable('activated', $secondData);
            $sent = $mail->build()->send();
            $sent = $mail2->build()->send();
            if ($sent) {
                Helper::to("activated.php?token=" . $token);
            } else {
                echo "Something went wrong";
                exit(1);
            }
        } else {
            echo "Something went wrong";
            exit(1);
        }
    } else {
        echo "Something went wrong";
        exit(1);
    }
} else {
    echo "user canceled agreement";
}
