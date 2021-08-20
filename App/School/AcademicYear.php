<?php

namespace App\School;

use Exception, App\Database\Database;

class AcademicYear
{

    private $id;
    private $academicYear;

    public function getId()
    {
        return $this->id;
    }

    public function getAcademicYearIn()
    {
        return $this->academicYear;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setAcademicYear($start, $end)
    {
        $this->academicYear = $start . "-" . $end;
    }


    public static function addAcademicYear(
        $acd,
        $start,
        $end,
        $school,
        $isCurrent
    ) {
        $db = Database::getInstance();
        $db->query(
            "SELECT academicYear FROM academic_year WHERE academicYear=? AND school=?"
        );
        $db->bind(1, $acd->getAcademicYearIn());
        $db->bind(2, $school);
        $db->execute();
        $count = $db->rowCount();
        if ($count == 0) {
            if ($end <= $start) {
                throw new Exception(
                    "The end of the academic year must be greater than the start"
                );
            }
            $currentDate = date("Y");
            $currentDate = intval($currentDate);
            if ($start < $currentDate) {
                throw new Exception(
                    "The start of the date is not valid.<br>It is less than the current year.<br>Current year: <b>$currentDate</b>"
                );
            }

            if ($isCurrent) {
                $db->query(
                    "UPDATE academic_year SET isCurrent=? WHERE school=?"
                );
                $db->bind(1, 0);
                $db->bind(2, $school);
                $db->execute();
            }

            $db->query(
                "INSERT INTO academic_year (startYear, endYear,academicYear,school,isCurrent) VALUES(?,?,?,?,?)"
            );
            $db->bind(1, $start);
            $db->bind(2, $end);
            $db->bind(3, $acd->getAcademicYearIn());
            $db->bind(4, $school);
            $db->bind(5, $isCurrent);
            $db->execute();
            return ($db->rowCount() > 0);
        }
        throw new Exception("Academic year already exists");
    }

    public static function updateAcademicYear(
        $id,
        $acd,
        $start,
        $end,
        $school,
        $isCurrent
    ) {
        $db = Database::getInstance();
        $db->query(
            "SELECT academicYear FROM academic_year WHERE academicYear=? AND school=? AND isCurrent=? AND academicYearId=?"
        );
        $db->bind(1, $acd->getAcademicYearIn());
        $db->bind(2, $school);
        $db->bind(3, $isCurrent);
        $db->bind(4, $id);
        $db->execute();
        $count = $db->rowCount();
        if ($count == 0) {
            if ($end <= $start) {
                throw new Exception(
                    "The end of the academic year must be greater than the start"
                );
            }
            $currentDate = date("Y");
            $currentDate = intval($currentDate);
            if ($start < $currentDate) {
                throw new Exception(
                    "The start of the date is not valid.<br>It is less than the current year.<br>Current year: <b>$currentDate</b>"
                );
            }

            if ($isCurrent) {
                $db->query(
                    "UPDATE academic_year SET isCurrent=? WHERE school=?"
                );
                $db->bind(1, 0);
                $db->bind(2, $school);
                $db->execute();
            }

            $db->query(
                "UPDATE academic_year SET startYear=?, endYear=?,academicYear=?,isCurrent=? WHERE school=? AND academicYearId=?"
            );
            $db->bind(1, $start);
            $db->bind(2, $end);
            $db->bind(3, $start . "-" . $end);
            $db->bind(4, $isCurrent);
            $db->bind(5, $school);
            $db->bind(6, $id);
            $db->execute();
            return ($db->rowCount() > 0);
        }
        throw new Exception("Change something to continue update.");
    }

    public static function getAcademicYear($school, $id)
    {
        $db = Database::getInstance();
        $data = $db->query(
            "SELECT * FROM academic_year WHERE school=? AND academicYearId=?"
        )
            ->bind(1, $school)
            ->bind(2, $id)
            ->single();
        if ($db->rowCount() > 0) {
            return $data;
        }
        return null;
    }

    public static function loadAcademicYears($id)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT * FROM academic_year WHERE school=? ORDER BY isCurrent DESC"
        );
        $db->bind(1, $id);
        $result = $db->resultset();
        if ($db->rowCount() > 0) {
            return $result;
        } else {
            throw new Exception("No record found");
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function getAcademicYearById($id)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT academicYear FROM academic_year WHERE academicYearId=?"
        );
        $db->bind(1, $id);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->academicYear;
        }
        return "";
    }

    public static function getAcademicYearId($year, $school)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT academicYearId FROM academic_year WHERE academicYear=? AND school=?"
        );
        $db->bind(1, $year);
        $db->bind(2, $school);
        $id = $db->single();
        if ($db->rowCount() > 0) {
            return $id->academicYearId;
        }
        return "";
    }

    public static function getCurrentYear($school)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT academicYear from academic_year WHERE isCurrent=? AND school=?"
        );
        $db->bind(1, 1);
        $db->bind(2, $school);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->academicYear;
        } else {
            return "";
        }
    }

    public static function getCurrentYearId($school)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT academicYearId from academic_year WHERE isCurrent=? AND school=?"
        );
        $db->bind(1, 1);
        $db->bind(2, $school);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->academicYearId;
        } else {
            return "";
        }
    }
}
