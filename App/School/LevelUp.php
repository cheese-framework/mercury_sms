<?php

namespace App\School;

use App\Database\Database;
use App\Core\Helper;
use Exception;

class LevelUp
{
  public static function level($studentId, $currentClass, $nextLevel, $currentYear, $nextYear)
  {
    $db = Database::getInstance();
    try {
      $classes = Helper::getClassList($studentId);
      $years = Helper::getYearList($studentId);
      if ($classes) {
        $list = explode(",", $classes);
        $list[] = $currentClass;
        $classes = implode(",", $list);
      } else {
        $classes = $currentClass . ",";
      }


      if ($years) {
        $list = explode(",", $years);
        $list[] = $currentYear;
        $years = implode(",", $list);
      } else {
        $years = $currentYear . ",";
      }


      $nextClass = Helper::getClassIdByLevel($nextLevel);
      $db->query("UPDATE students SET class=?, classes=?, mysubjects=?, academicYear=?, years=? WHERE studentId=?");

      $db->bind(1, $nextClass);
      $db->bind(2, $classes);
      $db->bind(3, NULL);
      $db->bind(4, $nextYear);
      $db->bind(5, $years);
      $db->bind(6, $studentId);
      $db->execute();
      // TODO: send mail to student telling them they have been promoted
    } catch (Exception $e) {
      throw $e;
    }
  }
}
