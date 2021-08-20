<?php

use App\Auth\Auth;
use App\Core\Helper;
use App\Notifiable\Mailable;

include_once "../init.php";

$data = json_decode(file_get_contents("php://input"), true);
$errors = [];

if ($data) {
  $username = $data['username'] ?? "";
  $email = $data['email'] ?? "";
  $password = $data['password'] ?? "";
  $role = $data['role'] ?? "";
  $gender = $data['gender'] ?? "";
  $acadqual = $data['acadqual'] ?? "";
  $profqual = $data['profqual'] ?? "";
  $contact = $data['contact'] ?? "";
  $yearAppointed = $data['yearappoint'] ?? "";
  $dob = $data['dob'] ?? "";
  $schoolId = $data['schoolId'] ?? "";

  if (!Helper::isEmpty($username, $email, $password, $role, $gender, $schoolId)) {
    $password = password_hash($password, PASSWORD_DEFAULT);
    $bool = Auth::userExists($email);
    if (!$bool) {
      try {
        $auth = new Auth(['twilio']);

        $token = Auth::generateToken();

        if ($auth->createStaff($username, $email, $password, $schoolId, $role, $gender, $dob, $profqual, $acadqual, $yearAppointed, $contact)) {
          Auth::addToken($token, $email);
          $welcomeData = [
            'to' => $email,
            'name' => $username,
            'from' => DEFAULT_FROM,
            'fromName' => DEFAULT_FULLNAME,
            'subject' => 'Verify Your Account',
            'message' => "Please confirm your email address by pressing the button below.<br>
                  Email = $email<br>
                  Password = " . $data['password'] . "",
            'link' => SCHOOL_URL . "/verify.php?token=$token"
          ];
          $mailable = new Mailable('welcome', $welcomeData);
          $sent = $mailable->build()->send();

          // send notice
          $auth->email = $email;

          if ($sent) {

            if (Helper::isUsingSMS($schoolId)) {
              $auth->notify("You have mail at " . $email . ". From " . Helper::getSchoolName($schoolId));
            }
            http_response_code(200);
            echo  json_encode(["success" => "Staff created successfully"]);
            return;
          } else {
            Auth::delete($email);
            $errors[] = "We could not send notification to: '<b>" . $email . "</b>'. So, we had to delete the user";
          }
        } else {
          $errors[] = "Something went wrong. Try again later";
        }
      } catch (Exception $ex) {
        $errors[] = $ex->getMessage();
      }
    } else {
      $errors[] = "E-Mail is in use by another user";
    }
  } else {
    $errors[] = "Fill in the required fields";
  }
  http_response_code(406);
  echo json_encode(["error" => $errors]);
} else {
  header("HTTP/1.1 404");
  echo json_encode(["error" => "Please specify all fields required"]);
}
