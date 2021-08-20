<?php

use PayPal\Api\Agreement;
use PayPal\Api\Payer;
use PayPal\Api\Plan;
use PayPal\Api\ShippingAddress;

// Create new agreement
$start_date = date("c", time() + 3600);
$agreement = new Agreement();
$agreement->setName('Activation of SMS - Mercury SMS')
    ->setDescription('Full activation of account to be enlisted as a valid customer')
    ->setStartDate($start_date);

// Set plan id
$plan = new Plan();
$plan->setId($planPatched->getId());
$agreement->setPlan($plan);

// Add payer type
$payer = new Payer();
$payer->setPaymentMethod('paypal');
$agreement->setPayer($payer);

// Adding shipping details
$shippingAddress = new ShippingAddress();
$shippingAddress->setLine1('111 First Street')
    ->setCity('Saratoga')
    ->setState('CA')
    ->setPostalCode('95070')
    ->setCountryCode('US');
$agreement->setShippingAddress($shippingAddress);


try {
    // Create agreement
    $agreement = $agreement->create($apiContext);

    // Extract approval URL to redirect user
    $approvalUrl = $agreement->getApprovalLink();
    header("Location:" . $approvalUrl);
} catch (PayPal\Exception\PayPalConnectionException $ex) {
    echo $ex->getCode();
    echo $ex->getData();
    die($ex);
} catch (Exception $ex) {
    die($ex);
}
