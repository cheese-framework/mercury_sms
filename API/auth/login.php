<?php

use App\Auth\Auth;
use App\Core\Helper;

include_once "../init.php";

$data = loadInput();

$email = $data['email'] ?? "";
$password = $data['password'] ?? "";

if (!Helper::isEmpty($email, $password)) {
  $auth = new Auth(['']);
  if ($user = $auth->loginStaff($email, $password)) {
    $isActivated = $auth->getStatus($email);
    $userData = [
      "username" => $user['username'],
      "id" => $user['id'],
      "role" => $user['role'],
      "avatar" => SCHOOL_URL . "/myschool/assets/profile/" . $user['pic'],
      "schoolId" => $user['school'],
      "email" => $user['email'],
      "isActivated" => $isActivated
    ];
    echo json_encode(["user" => $userData]);
    header("HTTP/1.1 200");
  } else {
    echo json_encode(["error" => "Invalid credentials"]);
    header("HTTP/1.1 406");
  }
} else {
  echo json_encode(["error" => "All fields are needed"]);
  header("HTTP/1.1 406");
}
