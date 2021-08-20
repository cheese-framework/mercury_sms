<?php

use App\Auth\Auth;
use App\Auth\Populator;
use App\Core\Helper;
use App\Notifiable\Mailable;

include_once "../init.php";


$data = loadInput();
$errors = [];

if ($data) {
  $schoolname = $data['schoolName'] ?? "";
  $username = $data['username'] ?? "";
  $email = $data['email'] ?? "";
  $password = $data['password'] ?? "";
  $schoolType = $data['schoolType'] ?? "";

  if (!Helper::isEmpty($schoolname, $username, $email, $password, $schoolType)) {
    // check if school name already exist
    if (Helper::schoolEmailExists($email)) {
      $errors[] = "This email is already in use by another school";
    } else {
      // check if user with that email already exist
      if (Auth::userExists($email)) {
        $errors[] = "Email address is already taken";
      } else {
        // hash the password
        $password = password_hash($password, PASSWORD_BCRYPT);
        $createdAt = date("Y-m-d");
        $populator = new Populator();
        $auth = new Auth(['']);
        $schoolId = $populator->createSchool($schoolname, $email, $createdAt, $schoolType);
        // if school was created
        if ($schoolId) {
          $token = Auth::generateToken();
          $hasCreatedUser = $auth->createStaff($username, $email, $password, $schoolId);
          // check if user was created
          if ($hasCreatedUser) {
            Auth::addToken($token, $email);
            $welcomeData = [
              'to' => $email,
              'name' => $username,
              'from' => DEFAULT_FROM,
              'fromName' => DEFAULT_FULLNAME,
              'subject' => 'Verify Your Account',
              'message' => "Thanks so much for joining us. You're on your way to get <b>Unbeatable experience</b> for your school management system with Mercury - SMS<br>Please confirm your email address by pressing the button below.",
              'link' => SCHOOL_URL . "/verify.php?token=$token"
            ];

            // try to send mail to the user

            try {
              $mailable = new Mailable('welcome', $welcomeData);
              $sent = $mailable->build()->send();
              if ($sent) {
                http_response_code(200);
                echo json_encode(["success" => "Congratulations!!! You have successfully created your school. Check your mail to complete the process."]);
                return;
              } else {
                $populator->deleteSchool($schoolId);
                Auth::delete($email);
                $errors[] = "Could not send you an email now. Please try again.";
              }
            } catch (\Throwable $th) {
              $errors[] = $th->getMessage();
            }
          } else {
            $errors[] = "Something went wrong while creating you as a staff in this school";
          }
        } else {
          $errors[] = "We could not create your school";
        }
      }
    }
  } else {
    $errors[] = "All fields are required";
  }

  echo json_encode(["error" => $errors]);
  header("HTTP/1.1 406");
} else {
  echo json_encode(["error" => "No data supplied"]);
  header("HTTP/1.1 404");
  // http_response_code(404);
}
