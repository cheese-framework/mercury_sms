<?php

// Simple Configs
const VOUCHER_CHARACTER_COUNT = 15;


// Database Configs

const DBNAME =  "sms";
const DBHOST = "localhost";
const DBUSER = "root";
const DBPASS =  "";

const DEFAULT_RECORD_PER_PAGE = 6;
const MAX_STUDENT_PER_CLASS_UNACTIVATED = 10;
const MAX_STUDENT_PER_CLASS_ACTIVATED = 50;


// Mail Configs

const SMTP_USERNAME = "mercurysms21@gmail.com";
const SMTP_PASSWORD = "2178056cletus";
const SMTP_PORT = 587;
const DEFAULT_FROM = SMTP_USERNAME;
const DEFAULT_FULLNAME = "Mercury School Management System";


/*** PAYPAL API CREDENTIALS **/

// SANDBOX CREDENTIALS

const CLIENT_ID = "ATBVqZ7hNm9N3hlbBzrULy9efk7rLOy5SooS-uneIykXG3EGlPbaJa-q0SF6oalk66Q6Rk-_vaIubsO4";
const SECRET = "EIxpqmf-1bONovx-L8arEHZpt22Ezi8KahpLOxpGpeEpbgJH3UCgeV9e2pdRpGbjFr1uLs9ki44eYsBa";

// LIVE ACCOUNT CREDENTIALS

const LIVE_CLIENT_ID = "AaEUM_6Qp3VJBK3Sf3GBo1jIxS99WEzhHCfp-f_vh-zhmlV3otC-lYq1d2ximyedk-1-bV0WmcHgiUMl";
const LIVE_SECRET = "EPPh7hVM9VvCXIC49Sgx6hBpna-tPn6k7UESCvHJ1gjjNNNBTWxhb7cs8q2lrdjDxRz1Dj73uBEu9idQ";

// Paypal Api Context

$apiContext = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential(
        CLIENT_ID,
        SECRET
    )
);

// Options Config Paypal API

$apiContext->setConfig([
    'mode' => 'sandbox',
    'log.LogEnabled' => false,
    'log.FileName' => '../PayPal.log',
    'log.LogLevel' => 'INFO', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
    'cache.enabled' => true
]);

// PUSHER CONFIGURATIONS

const PUSHER_ID =  '1190028';
const PUSHER_KEY = '3d43b2d8585b9a3e590c';
const PUSHER_SECRET = 'f11fd3a127a3fc483c6b';

// TWILLIO API
const TWILIO_KEY = "AC6ec5619daaae1868433338c21b565a36";
const TWILIO_SECRET = "bad818d30390fd9e6258455472b0920e";
const TWILIO_MESSAGING_SERVICE = "MGe8b76b2ed4a5b1222edcdb0f8a78ae18";
const TWILIO_NOTIFY_SID = "IS240c449d7e2b197bbf168795e717a53f";

// ENVS CONFIGURATIONS

const SCHOOL_URL = "http://localhost/sms";

const NURSERY_PRIMARY = 0;
const NURSERY_PRIMARY_JUNIOR = 1;
const JUNIOR_SENIOR = 2;
const JUNIOR = 3;
const SENIOR = 4;
const ALL = 5;

const SCHOOL_TYPES = [
    NURSERY_PRIMARY => "Nursery & Primary School",
    NURSERY_PRIMARY_JUNIOR => "Nursery, Primary & Junior Secondary School",
    JUNIOR_SENIOR => "Junior & Senior Secondary School",
    JUNIOR => "Junior Secondary School",
    SENIOR => "Senior Secondary School",
    ALL => "All"
];

const LEVELS = [
    "ALL" => "For All", 12 => 12, 11 => 11, 10 => 10, 9 => 9, 8 => 8, 7 => 7, 6 => 6, 5 => 5, 4 => 4, 3 => 3, 2 => 2, 1 => 1, "A3" => "Nursery 3", "A2" => "Nursery 2", "A1" => "Nursery 1", "Day" => "Daycare and Pre-Nursery"
];

const MONTH_CHARGE_WITH_SMS = 1020;
const MONTH_CHARGE_WITHOUT_SMS = 870;

const ANNUAL_SMS_SERVICE_CHARGE = 35.54;
const HALF_SMS_SERVICE_CHARGE = 25.67;
const THIRD_SMS_SERVICE_CHARGE = 17.65;


// DEBUG CONFIGURATIONS

const SHOW_ERROR_DETAIL = true;
