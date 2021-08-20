<?php

namespace App\Extra\Assessment;

use App\Core\Helper;
use App\Database\Database;
use App\Database\Finder;
use App\Database\Paginate;
use App\Helper\DateLib;
use App\School\Student;
use App\School\Subject;
use Exception;

class WorkshopAssessment
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    private function generateUniqueId()
    {
        return md5(uniqid("MERCURY_SMS_MANAGEMENT"));
    }

    private function hasAssessmentAddedToday($title, $date, $by)
    {
        $this->db->query("SELECT title,created_date FROM assessments WHERE title=? AND created_date=? AND created_by=?");
        $this->db->bind(1, $title);
        $this->db->bind(2, $date);
        $this->db->bind(3, $by);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public static function getDueLength($id)
    {
        $sql = Finder::select('assessments', 'dueLength')::where('id=:id')::getSql();
        return Database::getInstance()->query($sql)->bind(':id', $id)->single();
    }

    public static function delete($id)
    {
        $db = Database::getInstance();
        $db->query("DELETE FROM assessments WHERE id=?");
        $db->bind(1, $id);
        $db->execute();
    }

    public function getAssessment($id)
    {
        $this->db->query("SELECT * FROM assessments WHERE id=?");
        $this->db->bind(1, $id);
        $data = $this->db->single();
        if ($this->db->rowCount()  > 0) {
            return $data;
        }
        return FALSE;
    }

    public function addAssessment($title, $details, $file, $class, $subject, $due, $grade, $school, $created_by, $year, $date_created, $term)
    {
        $title = ucwords($title);
        if (!$this->hasAssessmentAddedToday($title, $date_created, $created_by)) {
            $id = $this->idExistsGiveAgain($this->generateUniqueId());
            // calculate dueLength and add validation
            $dateLib = new DateLib();
            $interval = $dateLib->interval($date_created, $due);
            $length = $interval->days;
            if ($length > 21) {
                $weeks = intval($length / 7);
                if ($weeks > 1) {
                    $weeks = $weeks . " weeks";
                } else {
                    $weeks = $weeks . " week";
                }
                throw new Exception("The due date can only be at most 3 weeks<br>{$weeks} supplied.", 403);
            }
            // we have a genuine id
            $this->db->query("INSERT INTO assessments (id,title,details,file,class,subject,due,grade,school,created_by,created_date,year,dueLength,term) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?);");
            $this->db->bind(1, $id);
            $this->db->bind(2, $title);
            $this->db->bind(3, $details);
            $this->db->bind(4, $file);
            $this->db->bind(5, $class);
            $this->db->bind(6, $subject);
            $this->db->bind(7, $due);
            $this->db->bind(8, $grade);
            $this->db->bind(9, $school);
            $this->db->bind(10, $created_by);
            $this->db->bind(11, $date_created);
            $this->db->bind(12, $year);
            $this->db->bind(13, $length);
            $this->db->bind(14, $term);
            if ($this->db->execute()) {
                return $id;
            }
            return FALSE;
        } else {
            throw new Exception("Found assessment with title: '<b>{$title}</b>' already added today");
        }
    }



    public function updateAssessment($title, $details, $file, $due, $grade, $date_created, $id)
    {
        $title = ucwords($title);
        // calculate dueLength and add validation
        $dateLib = new DateLib();
        $interval = $dateLib->interval($date_created, $due);
        $length = $interval->days;
        if ($length > 21) {
            $weeks = intval($length / 7);
            if ($weeks > 1) {
                $weeks = $weeks . " weeks";
            } else {
                $weeks = $weeks . " week";
            }
            throw new Exception("The due date can only be at most 3 weeks<br>{$weeks} supplied.", 403);
        }

        $this->db->query("UPDATE assessments SET title=?, details=?,file=?,grade=?,due=?,dueLength=? WHERE id=?");
        $this->db->bind(1, $title);
        $this->db->bind(2, $details);
        $this->db->bind(3, $file);
        $this->db->bind(4, $grade);
        $this->db->bind(5, $due);
        $this->db->bind(6, $length);
        $this->db->bind(7, $id);
        if ($this->db->execute()) {
            return TRUE;
        }
        return FALSE;
    }


    // recursive assessment generator

    private function idExistsGiveAgain($id)
    {
        if (!$this->idExist($id)) {
            return $id;
        } else {
            $this->idExistsGiveAgain($this->generateUniqueId());
        }
    }

    private function idExist($id)
    {
        $this->db->query("SELECT id FROM assessments WHERE id=?");
        $this->db->bind(1, $id);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    public static function getAssessments($school, $teacher, $limit, $lines)
    {
        $assessments = NULL;
        $sql = Finder::select('assessments')
            ->where('school=:school AND created_by=:by')->order('due', 'DESC');

        $paginate = new Paginate($sql::getSql(), $limit, $lines);
        foreach ($paginate->paginate(Database::getInstance(), [':school' => $school, ':by' => $teacher]) as $row) {
            $assessments[] = $row;
        }
        return $assessments;
    }
}
