<?php

require_once __DIR__ . "/../../config.php";
require_once __DIR__ . "/../../vendor/twilio/sdk/src/Twilio/Rest/Client.php";
require_once __DIR__ . "/../../App/Database/Database.php";
require_once __DIR__ . "/../../App/Core/Helper.php";
require_once __DIR__ . "/../../App/School/LevelUp.php";
require_once __DIR__ . "/../../App/Notifiable/Mail.php";
require_once __DIR__ . "/../../App/Notifiable/Mailable.php";
require_once __DIR__ . "/../../App/Notifiable/Notifiable.php";
require_once __DIR__ . "/../../App/Queue/TempStudent.php";

use App\School\LevelUp;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $data = json_decode(file_get_contents("php://input"), true);
  $fragments = $data['fragments'];
  for ($i = 0; $i < count($data['data']); $i++) {
    $action = $data['data'][$i]['decision'];
    $currentClass = $fragments['classId'];
    $nextYear = $fragments['nextYear'];
    $currentYear = $fragments['currentYear'];
    try {
      if ($action == 'up') {
        $nextLevel = $data['data'][$i]['stepper'] + $fragments['level'];
        LevelUp::level($data['data'][$i]['id'], $currentClass, $nextLevel, $currentYear, $nextYear);
      } else if ($action == 'down') {
        $nextLevel = $fragments['level'] - $data['data'][$i]['stepper'];
        LevelUp::level($data['data'][$i]['id'], $currentClass, $nextLevel, $currentYear, $nextYear);
      } else {
        $nextLevel = $fragments['level'];
        LevelUp::level($data['data'][$i]['id'], $currentClass, $nextLevel, $currentYear, $nextYear);
      }
    } catch (Exception $e) {
      echo $e->getMessage();
      continue;
    }
  }
}
