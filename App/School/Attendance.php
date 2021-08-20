<?php

namespace App\School;

use App\Database\Database;

class Attendance
{

    public static function addAttendance(
        $table,
        $check,
        $day,
        $date,
        $week,
        $status,
        $year,
        $value,
        $school,
        $class = 0,
        $time = 'Morning'
    ) {
        $db = Database::getInstance();
        if ($class > 0) {
            $data = $db->query(
                "SELECT id FROM {$table} WHERE date=? AND academicYear=? AND school=? AND {$check}=? AND time=?"
            )
                ->bind(1, $date)
                ->bind(2, $year)
                ->bind(3, $school)
                ->bind(4, $value)
                ->bind(5, $time)
                ->single();
            if ($db->rowCount() > 0) {
                // Run update
                $id = $data->id;
                return $db->query("UPDATE {$table} SET status=? WHERE id=?")
                    ->bind(1, $status)
                    ->bind(2, $id)
                    ->execute();
            } else {
                // Add new Attendance for Student
                return $db->query(
                    "INSERT INTO {$table} (day,date,week,academicYear,school,status,{$check},class,time) VALUES(?,?,?,?,?,?,?,?,?)"
                )
                    ->bind(1, $day)
                    ->bind(2, $date)
                    ->bind(3, $week)
                    ->bind(4, $year)
                    ->bind(5, $school)
                    ->bind(6, $status)
                    ->bind(7, $value)
                    ->bind(9, $time)
                    ->bind(8, $class)
                    ->execute();
            }
        } else {
            $data = $db->query(
                "SELECT id FROM {$table} WHERE date=? AND academicYear=? AND school=? AND {$check}=?"
            )
                ->bind(1, $date)
                ->bind(2, $year)
                ->bind(3, $school)
                ->bind(4, $value)
                ->single();
            if ($db->rowCount() > 0) {
                // Run update
                $id = $data->id;
                return $db->query("UPDATE {$table} SET status=? WHERE id=?")
                    ->bind(1, $status)
                    ->bind(2, $id)
                    ->execute();
            } else {
                // Add new Attendance for Staff
                return $db->query(
                    "INSERT INTO {$table} (day,date,week,academicYear,school,status,{$check}) VALUES(?,?,?,?,?,?,?)"
                )
                    ->bind(1, $day)
                    ->bind(2, $date)
                    ->bind(3, $week)
                    ->bind(4, $year)
                    ->bind(5, $school)
                    ->bind(6, $status)
                    ->bind(7, $value)
                    ->execute();
            }
        }
    }
}
