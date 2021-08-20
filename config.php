<?php

// Simple Configs
const VOUCHER_CHARACTER_COUNT = 15;
const FREE_TRIAL_DAYS = 90;

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
