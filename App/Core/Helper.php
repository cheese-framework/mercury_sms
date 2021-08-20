<?php

namespace App\Core;

use App\Auth\Auth;
use App\Database\Database;
use App\Database\Finder;
use App\Extra\Assessment\WorkshopAssessment;
use App\Helper\DateLib;
use App\I18n\Day;
use App\School\Student, App\School\Attendance, App\School\SMSParent, App\School\Subject;
use DateTime;
use Exception;
use ErrorException;
use UnexpectedValueException;


/** @noinspection ALL */
class Helper
{


    public static int $attend = 0;

    public static int $resId = 0;

    public function __construct($school)
    {
        try {
            Subject::loadSubjectsF($school);
        } catch (Exception $ex) {
        }
    }

    public static function isUsingSMS($school)
    {
        $db = Database::getInstance();
        $db->query("SELECT useSMS FROM school WHERE schoolId=?");
        $db->bind(1, $school);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            if ($data->useSMS == 1) {
                return true;
            }
            return false;
        }
        return false;
    }

    public static function isUsingOnlineAssessment($school)
    {
        $db = Database::getInstance();
        $db->query("SELECT allowOnlineAssessment FROM school WHERE schoolId=?");
        $db->bind(1, $school);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            if ($data->allowOnlineAssessment == 1) {
                return true;
            }
            return false;
        }
        return false;
    }

    public static function getSchoolType($school)
    {
        $db = Database::getInstance();
        $db->query("SELECT schoolType FROM school WHERE schoolId=?");
        $db->bind(1, $school);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return (is_numeric($data->schoolType)) ? $data->schoolType : NURSERY_PRIMARY;
        }
        return NURSERY_PRIMARY;
    }

    public static function isActivated($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT isActivated FROM school WHERE schoolId=?");
        $db->bind(1, $id);
        $result = $db->single();
        if ($db->rowCount() > 0) {
            return ($result->isActivated == 1) ? true : false;
        } else {
            return false;
        }
    }

    public static function activateOneMonthFreeOffer($school, $schoolEmail)
    {
        $db = Database::getInstance();
        $db->query("UPDATE school SET isActivated=1 WHERE schoolId=?");
        $db->bind(1, $school);
        $db->execute();
        if ($db->rowCount() > 0) {
            $startDate = date('Y-m-d H:i:s');
            $db->query("INSERT INTO billings (paymentId, frequency, currency,amount,cycles,billingInterval,status,payerEmail,username,payerId,startDate) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
            $db->bind(1, substr(Auth::generateToken(), 0, 10));
            $db->bind(2, "MONTH");
            $db->bind(3, "USD");
            $db->bind(4, 0.00);
            $db->bind(5, 1);
            $db->bind(6, 1);
            $db->bind(7, "Active");
            $db->bind(8, $schoolEmail);
            $db->bind(9, $schoolEmail);
            $db->bind(10, $school);
            $db->bind(11, $startDate);
            $db->execute();
            if ($db->rowCount() > 0) {
                $id = $db->lastInsertId();
                $db->query("UPDATE school SET payment=?, useSMS=? WHERE schoolId=?");
                $db->bind(1, $id);
                $db->bind(2, 1);
                $db->bind(3, $school);
                $db->execute();
            }
        }
    }

    public static function schoolEmailExists($email)
    {
        $db = Database::getInstance();
        $db->query("SELECT schoolEmail FROM school WHERE schoolEmail=?");
        $db->bind(1, $email);
        $db->execute();
        return $db->rowCount() > 0;
    }

    public static function isEmpty(...$args)
    {
        for ($i = 0; $i < count($args); $i++) {
            if (trim($args[$i]) == "") {
                return true;
            }
        }
        return false;
    }

    public static function undoList($str)
    {
        $list = explode(",", $str);
        $string = "";
        if (count($list) > 0) {
            foreach ($list as $l) {
                $string .= $l . "<br>";
            }
            return $string;
        } else {
            return "";
        }
    }

    public static function getSchoolName($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT schoolName FROM school WHERE schoolId=?");
        $db->bind(1, $id);
        $data = $db->single();
        return $data->schoolName;
    }


    public static function getSchoolEmail($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT schoolEmail FROM school WHERE schoolId=?");
        $db->bind(1, $id);
        $data = $db->single();
        return $data->schoolEmail;
    }

    public static function updateSchoolName($name, $school)
    {
        $db = Database::getInstance();
        $db->query("UPDATE school SET schoolName=? WHERE schoolId=?");
        $db->bind(1, $name);
        $db->bind(2, $school);
        $db->execute();
    }

    public static function getTeachers($school)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT * FROM staffs WHERE staff_role='Teacher' AND school=? ORDER BY staff_name ASC"
        );
        $db->bind(1, $school);
        $data = $db->resultset();
        if ($db->rowCount() > 0) {
            return $data;
        } else {
            throw new Exception();
        }
    }

    public static function getTeacher($list)
    {
        $db = Database::getInstance();
        $list = explode(",", $list);
        $temp = "";
        if ($list != null) {
            foreach ($list as $l) {
                $db->query("SELECT * FROM staffs WHERE staffId=?");
                $db->bind(1, $l);
                $data = $db->single();
                if ($db->rowCount() > 0) {
                    $temp .= $data->staff_name . ",";
                }
            }
        }
        return Helper::makeList($temp);
    }

    public static function makeList($str)
    {
        $string = explode(",", $str);
        $tempStr = "";
        $arr = [];

        if (count($string) > 1) {
            foreach ($string as $s) {
                if (trim($s) != "") {
                    $arr[] = trim($s);
                }
            }
            $tempStr = implode(',', $arr);

            return $tempStr;
        } else {
            return $str;
        }
    }

    public static function prettify(...$data)
    {
        foreach ($data as $datum) {
            echo "<pre class='alert alert-info'>";
            echo "<h5 class='alert-heading'>Details: </h5>";
            print_r($datum);
            echo "</pre>";
        }
        // exit;
    }

    public static function dump($data)
    {
        echo "<pre class='alert alert-info'>";
        echo "<h5 class='alert-heading'>Details: </h5>";
        var_dump($data);
        echo "</pre>";
        exit;
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function showErrorPage()
    {
        Helper::to("404.php");
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function showNotPermittedPage()
    {
        Helper::to("403.php");
    }

    public static function to($path)
    {
        header("Location: $path");
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function addClass(
        $class,
        $classTeacher,
        $classAcademicYear,
        $classSubjects,
        $school,
        $choose,
        $levels
    ) {
        $db = Database::getInstance();
        $db->query("SELECT * FROM classes WHERE className=? AND school=?");
        $db->bind(1, $class)->bind(2, $school);
        $db->execute();
        if ($db->rowCount() == 0) {
            $db->query(
                "INSERT INTO classes (className, classTeacher,classAcademicYear,classSubjects,school,choose,levels) VALUES(?,?,?,?,?,?,?)"
            );
            $db->bind(1, $class);
            $db->bind(2, $classTeacher);
            $db->bind(3, $classAcademicYear);
            $db->bind(4, $classSubjects)->bind(5, $school)->bind(6, $choose)->bind(7, $levels);
            $db->execute();

            if ($db->rowCount() > 0) {
                return true;
            } else {
                throw new Exception("Something went wrong");
            }
        } else {
            throw new Exception("This class already exist");
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function updateClass(
        $class,
        $classTeacher,
        $classSubjects,
        $id,
        $choose,
        $levels
    ) {
        $db = Database::getInstance();
        $db->query(
            "UPDATE classes SET className=?,classTeacher=?,classSubjects=?,choose=?,levels=? WHERE classId=?"
        );
        $db->bind(1, $class);
        $db->bind(2, $classTeacher);
        $db->bind(3, $classSubjects);
        $db->bind(4, $choose);
        $db->bind(5, $levels);
        $db->bind(6, $id);
        $db->execute();

        if ($db->rowCount() > 0) {
            return true;
        } else {
            throw new Exception("Something went wrong (No update was executed)");
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function loadClasses($school)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM classes WHERE school=? ORDER BY className");
        $db->bind(1, $school);
        $data = $db->resultset();

        if ($db->rowCount() > 0) {
            return $data;
        } else {
            throw new Exception("No record found!");
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function loadClass($school)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT * FROM classes WHERE school=? ORDER BY className ASC"
        )->bind(
            1,
            $school
        );
        $data = $db->resultset();

        if ($db->rowCount() > 0) {
            return $data;
        } else {
            throw new Exception("No record found!");
        }
    }

    public static function getNumRecords($table)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM $table");
        $db->execute();
        return $db->rowCount();
    }

    /*
     * ********************************************************************
     * SIMPLE HELPER FUNCTIONS
     * *************************************************************************
     */
    public static function returnClassList($id, $school)
    {
        $db = Database::getInstance();
        $db->query("SELECT classTeacher,classId FROM classes WHERE school=?")->bind(
            1,
            $school
        );
        $result = $db->resultset();
        $classList = [];
        if ($result != null) {
            foreach ($result as $res) {
                $list = explode(",", $res->classTeacher);
                foreach ($list as $l) {
                    if ($l == $id) {
                        $classList[] = $res->classId;
                    }
                }
            }
        } else {
            $classList = [];
        }
        return $classList;
    }

    public static function getNumStaffs($gender, $school)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM staffs WHERE gender=? AND school=?");
        $db->bind(1, $gender);
        $db->bind(2, $school);
        $db->execute();
        return $db->rowCount();
    }




    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function addResult(
        $term,
        $year,
        $subject,
        $student,
        $staff,
        $sheet,
        $total,
        $exam,
        $school
    ) {
        $db = Database::getInstance();
        $db->query(
            "SELECT * FROM results WHERE student_id=? AND subject_id=? AND academicYear=? AND term=? AND school=?"
        );
        $db->bind(1, $student);
        $db->bind(2, $subject);
        $db->bind(3, $year);
        $db->bind(4, $term);
        $db->bind(5, $school);
        $db->single();
        if ($db->rowCount() > 0) {
            throw new ErrorException(
                "Result already found for student. Please consider updating."
            );
        } else {
            if ($total <= Helper::getPassMark($subject)) {

                $db->query(
                    "INSERT INTO results (term,academicYear,subject_id,student_id,class_id,sheets,exam,school) VALUES(?,?,?,?,?,?,?,?)"
                );
                $db->bind(1, $term);
                $db->bind(2, $year);
                $db->bind(3, $subject);
                $db->bind(4, $student);
                $db->bind(5, $staff);
                $db->bind(6, $sheet);
                $db->bind(7, $exam);
                $db->bind(8, $school);
                $db->execute();
                $bool = $db->rowCount() > 0;
                self::addTotal($student, $year, $term, $total, $staff, $school);
                return $bool;
            } else {
                throw new Exception(
                    "Total cannot exceed " . Helper::getPassMark($subject)
                );
            }
        }
    }

    public static function getAssessmentMark($sub)
    {
        $db = Database::getInstance();
        $db->query("SELECT assessment FROM subjects WHERE subjectId=?")
            ->bind(1, $sub);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->assessment;
        }
        return 0;
    }

    public static function getPassMark($sub)
    {
        $db = Database::getInstance();
        $db->query("SELECT finalGrade FROM subjects WHERE subjectId=?");
        $db->bind(1, $sub);
        $data = $db->single();
        $assessment = self::getAssessmentMark($sub);
        if ($db->rowCount() > 0) {
            return ($data->finalGrade - $assessment);
        } else {
            return (100 - $assessment);
        }
    }

    private static function addTotal(
        $studentId,
        $academicYear,
        $term,
        $total,
        $class,
        $school
    ) {
        $db = Database::getInstance();
        $db->query(
            "SELECT total FROM totals WHERE studentId=? AND term=? AND academicYear=? AND school=?"
        );
        $db->bind(1, $studentId);
        $db->bind(2, $term);
        $db->bind(3, $academicYear);
        $db->bind(4, $school);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            $prevTotal = $data->total;
            $db->query(
                "UPDATE totals SET total=? WHERE term=? AND academicYear=? AND studentId=? AND school=?"
            );
            $db->bind(1, $prevTotal + $total);
            $db->bind(2, $term);
            $db->bind(3, $academicYear);
            $db->bind(4, $studentId);
            $db->bind(5, $school);
        } else {
            $db->query(
                "INSERT INTO totals (studentId,academicYear,term,total,class,school) VALUES(?,?,?,?,?,?)"
            );
            $db->bind(1, $studentId);
            $db->bind(2, $academicYear);
            $db->bind(3, $term);
            $db->bind(4, $total);
            $db->bind(5, $class);
            $db->bind(6, $school);
        }

        $db->execute();
    }

    public static function returnClass($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM classes WHERE classId=?");
        $db->bind(1, $id);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data;
        } else {
            throw new Exception();
        }
    }


    public static function getResult($year, $term, $subject, $studentId, $school)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT * FROM results WHERE term=? AND academicYear=? AND subject_id=? AND student_id=? AND school=?"
        );
        $db->bind(1, $term);
        $db->bind(2, $year);
        $db->bind(3, $subject);
        $db->bind(4, $studentId);
        $db->bind(5, $school);
        $record = $db->single();
        if ($db->rowCount() > 0) {
            self::$resId = $record->resultId;
            $actGrades = explode(",", $record->sheets);
            $rec = [];
            foreach ($actGrades as $act) {
                $temp = explode(":", $act);
                array_push($rec, $temp);
            }
            return [
                $rec,
                $record->exam,
                $record->assessment
            ];
        }
        return null;
    }

    public static function getRemark($mark, $school)
    {
        $data = self::getRemarks($school);
        if ($data != "") {
            foreach ($data as $d) {
                if ($mark >= $d->min && $mark <= $d->max) {
                    return $d->remark;
                }
            }
        } else {
            return "";
        }
    }

    public static function getRemarks($school)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM remarks WHERE school=? ORDER BY max DESC");
        $db->bind(1, $school);
        $data = $db->resultset();
        return ($data != null) ? $data : "";
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function changeResult(
        $student,
        $resultID,
        $sheets,
        $prev,
        $year,
        $term,
        $new,
        $exam,
        $subject
    ) {
        $db = Database::getInstance();
        $finalMark = self::getFinalMark($resultID);
        $assessmentGrade = Helper::getAssessmentMark($subject);
        if ($new <= ($finalMark - $assessmentGrade)) {
            $db->query("UPDATE results SET sheets=?, exam=? WHERE resultId=?");
            $db->bind(1, $sheets);
            $db->bind(2, $exam);
            $db->bind(3, $resultID);
            $db->execute();
            if ($db->rowCount() > 0) {
                $t_id = self::getTotalId($student, $year, $term);
                if ($t_id != "") {
                    self::updateTotal($t_id, $prev, $new);
                }
            }
        } else {
            throw new Exception(
                "Total cannot be more than the final grade: <b>" . ($finalMark - $assessmentGrade) .
                    "</b>"
            );
        }
    }




    // change result with assessment
    public static function changeResultForAssessment(
        $student,
        $year,
        $term,
        $subject,
        $list,
        $assessment,
        $school
    ) {
        $db = Database::getInstance();
        $sheets = Helper::getResult($year, $term, $subject, $student, $school);
        $prev = 0;
        if ($sheets) {
            $prev = $sheets[2]; // assessment grade
        } else {
            $prev = 0;
        }


        $studentTotal = self::getTotal($student, $year, $term);
        $newTotal = ($studentTotal - $prev) + $assessment;

        $db->query("UPDATE results SET assessment=?, assessment_list=? WHERE student_id=? AND subject_id=? AND academicYear=? AND term=? AND school=?");
        $db->bind(1, $assessment);
        $db->bind(2, $list);
        $db->bind(3, $student);
        $db->bind(4, $subject);
        $db->bind(5, $year);
        $db->bind(6, $term);
        $db->bind(7, $school);
        $db->execute();
        if ($db->rowCount() > 0) {
            $db->query("UPDATE totals SET total=? WHERE academicYear=? AND term=? AND studentId=? AND school=?");
            $db->bind(1, $newTotal);
            $db->bind(2, $year);
            $db->bind(3, $term);
            $db->bind(4, $student);
            $db->bind(5, $school);
            $db->execute();
        }
    }






    public static function getFinalMark($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT subject_id FROM results WHERE resultId=?");
        $db->bind(1, $id);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            $subId = $data->subject_id;
            $db->query("SELECT finalGrade FROM subjects WHERE subjectId=?");
            $db->bind(1, $subId);
            $data = $db->single();
            if ($db->rowCount() > 0) {
                return $data->finalGrade;
            } else {
                return 100;
            }
        } else {
            return 100;
        }
    }

    private static function getTotalId($studentId, $academicYear, $term)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT totalId FROM totals WHERE studentId=? AND academicYear=? AND term=?"
        );
        $db->bind(1, $studentId);
        $db->bind(2, $academicYear);
        $db->bind(3, $term);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->totalId;
        } else {
            return "";
        }
    }

    private static function getTotal($studentId, $academicYear, $term)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT total FROM totals WHERE studentId=? AND academicYear=? AND term=?"
        );
        $db->bind(1, $studentId);
        $db->bind(2, $academicYear);
        $db->bind(3, $term);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->total;
        } else {
            return 0;
        }
    }

    private static function updateTotal($id, $prev, $new)
    {
        $db = Database::getInstance();
        $db->query("SELECT total FROM totals WHERE totalId=?");
        $db->bind(1, $id);
        $tot = $db->single();
        if ($db->rowCount() > 0) {
            $total = $tot->total;
            $curr = $total - $prev;
            $updated = $curr + $new;
            $db->query("UPDATE totals SET total=? WHERE totalId=?");
            $db->bind(1, $updated);
            $db->bind(2, $id);
            $db->execute();
        }
    }

    public static function createRemark($min, $max, $remark, $school)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM remarks WHERE min=? OR max=? OR remark=? AND school=?");
        $db->bind(1, $min);
        $db->bind(2, $max);
        $db->bind(3, $remark);
        $db->bind(4, $school);
        $db->execute();
        if ($db->rowCount() <= 0) {
            $db->query(
                "INSERT INTO remarks (min, max, remark, school) VALUES(?,?,?,?)"
            );
            $db->bind(1, $min);
            $db->bind(2, $max);
            $db->bind(3, $remark);
            $db->bind(4, $school);
            $db->execute();
        } else {
            throw new Exception("Remark is already available");
        }
    }

    public static function updateRemark($id, $min, $max, $remark)
    {
        $db = Database::getInstance();
        $db->query("UPDATE remarks SET min=?,max=?,remark=? WHERE remarkId=?");
        $db->bind(1, $min);
        $db->bind(2, $max);
        $db->bind(3, $remark);
        $db->bind(4, $id);
        $db->execute();
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function getStaffRecords($school)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM staffs WHERE school=?");
        $db->bind(1, $school);
        $data = $db->resultset();
        if ($db->rowCount() > 0) {
            return $data;
        }
        throw new Exception("No record found!");
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function getStaffRecord($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM staffs WHERE staffId=?");
        $db->bind(1, $id);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data;
        }
        throw new Exception("No record found!");
    }

    public static function updateStaffRecord(
        $username,
        $email,
        $role,
        $gender,
        $dob,
        $prof,
        $acad,
        $year,
        $contact,
        $id
    ) {
        $db = Database::getInstance();
        $db->query(
            "UPDATE staffs SET staff_name=?,staff_email=?,staff_role=?,gender=?,dob=?,profqual=?,acadqual=?,yearappoint=?,contact_address=? WHERE staffId=?"
        );
        $db->bind(1, $username);
        $db->bind(2, $email);
        $db->bind(3, $role);
        $db->bind(4, $gender);
        $db->bind(5, $dob);
        $db->bind(6, $prof);
        $db->bind(7, $acad);
        $db->bind(8, $year);
        $db->bind(9, $contact);
        $db->bind(10, $id);
        $db->execute();
    }

    public static function deleteStaff($id)
    {
        $db = Database::getInstance();
        $db->query("DELETE FROM staffs WHERE staffId=?");
        $db->bind(1, $id);
        $db->execute();
    }

    public static function deleteRemark($id, $school)
    {
        $db = Database::getInstance();
        $db->query("DELETE FROM remarks WHERE remarkId=? AND school=?");
        $db->bind(1, $id);
        $db->bind(2, $school);
        $db->execute();
    }

    public static function getRemarkId($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM remarks WHERE remarkId=?");
        $db->bind(1, $id);
        $data = $db->single();
        return $data;
    }


    public static function changePicture($id, $img, $prev)
    {
        $db = Database::getInstance();
        $db->query("UPDATE staffs SET staff_photo =? WHERE staffId=?");
        $db->bind(1, $img);
        $db->bind(2, $id);
        $db->execute();
        $image = explode('/', $prev);
        if (strtolower(end($image)) != "default.jpeg") {
            if (file_exists($prev)) {
                @@unlink($prev);
            }
        }
    }

    public static function getClassCount($classId, $year, $id)
    {
        $db = Database::getInstance();
        $db->query("SELECT studentId FROM students WHERE class=? AND academicYear=? AND school=?");
        $db->bind(1, $classId);
        $db->bind(2, $year);
        $db->bind(3, $id);
        $db->execute();
        return $db->rowCount();
    }

    public static function changeBadge($id, $img)
    {
        $db = Database::getInstance();
        $db->query("UPDATE school SET schoolBadge =? WHERE schoolId=?");
        $db->bind(1, $img);
        $db->bind(2, $id);
        $db->execute();
    }

    public static function getBagde($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT schoolBadge FROM school WHERE schoolId=?");
        $db->bind(1, $id);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->schoolBadge;
        } else {
            return "";
        }
    }

    public static function updatePassword($password, $id)
    {
        $db = Database::getInstance();
        $db->query("UPDATE staffs SET staff_password=? WHERE staffId=?");
        $db->bind(1, $password);
        $db->bind(2, $id);
        $db->execute();
    }

    public static function addAttendance(
        $day,
        $date,
        $week,
        $present,
        $absent,
        $year,
        $class
    ) {
        $db = Database::getInstance();
        $db->query(
            "SELECT * FROM attendance WHERE date=? AND class=? AND academicYear=?"
        );
        $db->bind(1, $date);
        $db->bind(2, $class);
        $db->bind(3, $year);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            throw new Exception(
                "Attendance already added today. Have you tried updating? <a href='updateattendance.php?id=$data->attendanceId&class=$data->class'>Update here</a>"
            );
        } else {
            $db->query(
                "INSERT INTO attendance(day,date,week,present,absent,academicYear,class) VALUES(?,?,?,?,?,?,?)"
            );
            $db->bind(1, $day);
            $db->bind(2, $date);
            $db->bind(3, $week);
            $db->bind(4, $present);
            $db->bind(5, $absent);
            $db->bind(6, $year);
            $db->bind(7, $class);
            $db->execute();
        }
    }

    public static function hasBeenMarked($date, $year, $school, $staff)
    {
        $db = Database::getInstance();
        $data = $db->query(
            "SELECT status FROM staffattendance WHERE date=? AND academicYear=? AND school=? AND staff=?"
        )
            ->bind(1, $date)
            ->bind(2, $year)
            ->bind(3, $school)
            ->bind(4, $staff)
            ->single();
        if ($db->rowCount() > 0) {
            return $data->status;
        }
        return false;
    }

    public static function hasBeenMarkedStudent(
        $date,
        $year,
        $school,
        $student,
        $time
    ) {
        $db = Database::getInstance();
        $data = $db->query(
            "SELECT status FROM attendance WHERE date=? AND academicYear=? AND school=? AND student=? AND time=?"
        )
            ->bind(1, $date)
            ->bind(2, $year)
            ->bind(3, $school)
            ->bind(4, $student)
            ->bind(5, $time)
            ->single();
        if ($db->rowCount() > 0) {
            return $data->status;
        }
        return false;
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function addStaffAttendance(
        $day,
        $date,
        $week,
        $status,
        $year,
        $staff,
        $school
    ) {
        return Attendance::addAttendance(
            "staffattendance",
            "staff",
            $day,
            $date,
            $week,
            $status,
            $year,
            $staff,
            $school
        );
    }

    public static function addStudentAttendance(
        $day,
        $date,
        $week,
        $status,
        $year,
        $student,
        $school,
        $class,
        $time
    ) {
        return Attendance::addAttendance(
            "attendance",
            "student",
            $day,
            $date,
            $week,
            $status,
            $year,
            $student,
            $school,
            $class,
            $time
        );
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function loadDaily($year, $date, $school, $status, $time, $class)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT * FROM attendance WHERE academicYear=? AND date=? AND school=? AND status=? AND time=? AND class=?"
        );
        $db->bind(1, $year);
        $db->bind(2, $date);
        $db->bind(3, $school)
            ->bind(4, $status)
            ->bind(5, $time)
            ->bind(6, $class)
            ->single();
        if ($db->rowCount() > 0) {
            return $db->rowCount();
        } else {
            return null;
        }
    }

    public static function loadStaffDaily($year, $date, $school, $status)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT * FROM staffattendance WHERE academicYear=? AND date=? AND school=? AND status=?"
        );
        $db->bind(1, $year)
            ->bind(2, $date)
            ->bind(3, $school)
            ->bind(4, $status)
            ->execute();
        if ($db->rowCount() > 0) {
            return $db->rowCount();
        } else {
            return null;
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function loadWeekly(
        $weekNo,
        $year,
        $school,
        $status,
        $day,
        $class
    ) {
        $db = Database::getInstance();
        if ($class > 0) {
            $db->query(
                "SELECT * FROM attendance WHERE week=? AND academicYear=? AND school=? AND status=? AND day=? AND class=?"
            );
            $db->bind(1, $weekNo);
            $db->bind(2, $year);
            $db->bind(3, $school);
            $db->bind(4, $status);
            $db->bind(5, $day);
            $db->bind(6, $class);
        } else {
            $db->query(
                "SELECT * FROM attendance WHERE week=? AND academicYear=? AND school=? AND status=? AND day=?"
            );
            $db->bind(1, $weekNo);
            $db->bind(2, $year);
            $db->bind(3, $school);
            $db->bind(4, $status);
            $db->bind(5, $day);
        }
        $db->execute();
        if ($db->rowCount() > 0) {
            return $db->rowCount();
        } else {
            return 0;
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function loadStaffWeekly(
        $weekNo,
        $year,
        $school,
        $status,
        $day
    ) {
        $db = Database::getInstance();
        $db->query(
            "SELECT * FROM staffattendance WHERE week=? AND academicYear=? AND school=? AND status=? AND day=?"
        );
        $db->bind(1, $weekNo);
        $db->bind(2, $year);
        $db->bind(3, $school);
        $db->bind(4, $status);
        $db->bind(5, $day);
        $db->execute();
        if ($db->rowCount() > 0) {
            return $db->rowCount();
        } else {
            return 0;
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function getStatistics($day, $year, $class, $status, $time, $school)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT * FROM attendance WHERE date=? AND academicYear=? AND class=? AND status=? AND time=? AND school=?"
        );
        $db->bind(1, $day);
        $db->bind(2, $year);
        $db->bind(3, $class);
        $db->bind(4, $status);
        $db->bind(5, $time);
        $db->bind(6, $school);
        $db->execute();
        if ($db->rowCount() > 0) {
            return $db->rowCount();
        } else {
            return 0;
        }
    }


    public static function getWeeklyStatistics($week, $year, $class, $status, $time, $school)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT * FROM attendance WHERE week=? AND academicYear=? AND class=? AND status=? AND time=? AND school=?"
        );
        $db->bind(1, $week);
        $db->bind(2, $year);
        $db->bind(3, $class);
        $db->bind(4, $status);
        $db->bind(5, $time);
        $db->bind(6, $school);
        $db->execute();
        if ($db->rowCount() > 0) {
            return $db->rowCount();
        } else {
            return 0;
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function updateAttendance($id, $present, $absent)
    {
        $db = Database::getInstance();
        $db->query(
            "UPDATE attendance SET present=?, absent=? WHERE attendanceId=?"
        );
        $db->bind(1, $present);
        $db->bind(2, $absent);
        $db->bind(3, $id);
        $db->execute();
    }

    public static function updateStaffAttendance($id, $present, $absent)
    {
        $db = Database::getInstance();
        $db->query("UPDATE staffAttendance SET present=?, absent=? WHERE id=?");
        $db->bind(1, $present);
        $db->bind(2, $absent);
        $db->bind(3, $id);
        $db->execute();
    }

    public static function addExpenses($amount, $date, $type, $year, $school)
    {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO expenses (amount, date, type,academic_year,school) VALUES(?,?,?,?,?)"
        );
        $db->bind(1, $amount);
        $db->bind(2, $date);
        $db->bind(3, $type);
        $db->bind(4, $year);
        $db->bind(5, $school);
        $db->execute();
    }

    public static function getExpenses($school)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM expenses WHERE school=?");
        $db->bind(1, $school);
        $data = $db->resultset();
        if ($db->rowCount() > 0) {
            return $data;
        } else {
            throw new Exception("Nothing here");
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function addIncomes($amount, $date, $type, $year, $school)
    {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO incomes (amount, date, type,academic_year,school) VALUES(?,?,?,?,?)"
        );
        $db->bind(1, $amount);
        $db->bind(2, $date);
        $db->bind(3, $type);
        $db->bind(4, $year);
        $db->bind(5, $school);
        $db->execute();
    }

    public static function getIncomes($school)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM incomes WHERE school=? ORDER BY date DESC");
        $db->bind(1, $school);
        $data = $db->resultset();
        if ($db->rowCount() > 0) {
            return $data;
        } else {
            throw new Exception("Nothing here");
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function remove($table, $col, $id)
    {
        $db = Database::getInstance();
        $db->query("DELETE FROM $table WHERE $col=?");
        $db->bind(1, $id);
        $db->execute();
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function addFeeRange($class, $fee, $year, $school)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM feerange WHERE year=? AND school=?");
        $db->bind(1, $year);
        $db->bind(2, $school);
        $data = $db->resultset();
        $canContinue = true;
        if ($db->rowCount() > 0) {
            foreach ($data as $d) {
                $classList = explode(",", $class);
                $dbClassList = explode(",", $d->class);
                foreach ($classList as $cls) {
                    if (in_array($cls, $dbClassList)) {
                        $canContinue = false;
                        throw new Exception(
                            "Fee has already been allocated for the selected class <b>{" .
                                Helper::classEncode($cls) .
                                "}</b> in this academic year. Consider updating."
                        );
                    }
                }
            }
            if ($canContinue) {
                $db->query(
                    "INSERT INTO feerange (class,fee,year,school) VALUES(?,?,?,?) "
                );
                $db->bind(1, $class);
                $db->bind(2, $fee);
                $db->bind(3, $year);
                $db->bind(4, $school);
                $db->execute();
            } else {
                throw new Exception(
                    "Something went wrong when trying to talk with the database."
                );
            }
        } else {
            $db->query(
                "INSERT INTO feerange (class,fee,year,school) VALUES(?,?,?,?) "
            );
            $db->bind(1, $class);
            $db->bind(2, $fee);
            $db->bind(3, $year);
            $db->bind(4, $school);
            $db->execute();
        }
    }

    public static function classEncode($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT className FROM classes WHERE classId=?");
        $db->bind(1, $id);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->className;
        } else {
            return "";
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function getFeeRange($school)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM feerange WHERE school=?");
        $db->bind(1, $school);
        $data = $db->resultset();
        if ($db->rowCount() > 0) {
            return $data;
        } else {
            throw new Exception("No record found");
        }
    }

    public static function getFeeDetails($studentId, $class, $year, $term, $school)
    {
        $db = Database::getInstance();
        $db->query("SELECT feeToPay,hasSpecial FROM fees WHERE studentId=? AND classId=? AND term=? AND academicYear=? AND school=?");
        $db->bind(1, $studentId);
        $db->bind(2, $class);
        $db->bind(3, $term);
        $db->bind(4, $year);
        $db->bind(5, $school);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data;
        }
        return NULL;
    }

    public static function generateFeesTable($class, $year, $term, $school)
    {
        $feeToPay = Helper::generateFee($class, $year, $school);
        if ($feeToPay > 0) {
            try {
                self::insertFeeData($class, $year, $term, $school);
                $db = Database::getInstance();
                $db->query(
                    "SELECT * FROM fees WHERE classId=? AND academicYear=? AND term=? ORDER BY paid DESC"
                );
                $db->bind(1, $class);
                $db->bind(2, $year);
                $db->bind(3, $term);
                $data = $db->resultset();
                $str = "<h3>Fees Details for " . Helper::classEncode($class) .
                    "</h3>" .
                    "<p class='text-info'>Please scroll left and right if on smaller screen resolution.</p>";
                if ($db->rowCount() > 0) {
                    if ($data != null) {
                        $str .= "<table class='table table-striped table-hover table-bordered dt'>";
                        $str .= "<thead>" . "<tr>" .
                            "<th class='sticky-top'>Student</th>" .
                            "<th class='sticky-top'>Payment Details</th>" .
                            "<th class='sticky-top'>Balance</th>" .
                            "<th class='sticky-top'>Paid</th>" .
                            "<th class='sticky-top'>Initial Bill</th>" .
                            "<th class='sticky-top'>Update Fee</th>" . "</tr>" .
                            "</thead>" . "<tbody>";
                        foreach ($data as $d) {
                            $feeData = self::getFeeDetails($d->studentId, $class, $year, $term, $school);

                            if ($feeData != NULL) {
                                $hasSpecial = ($feeData->hasSpecial == 1) ? TRUE : FALSE;
                                if ($hasSpecial) {
                                    $feeToPay = $feeData->feeToPay;
                                } else {
                                    $feeToPay = Helper::generateFee($class, $year, $school);
                                }
                            } else {
                                $feeToPay = Helper::generateFee($class, $year, $school);
                            }

                            $details = "";
                            $temp = explode(",", $d->details);
                            $tip = "";
                            $label = "";
                            $bal = $feeToPay - $d->paid;

                            if ($d->paid < $feeToPay) {
                                $color = "text-danger";
                                $label = "<i class='mdi mdi-window-close'></i>";
                            } else if ($d->paid > $feeToPay) {
                                $tip = "(Paid " . self::moneySign($school) . str_replace("-", "", $bal) .
                                    " more than due)";
                                $color = "text-info";
                                $label = "<i class='mdi mdi-check-all'></i>";
                            } else {
                                $color = "text-success";
                                $label = "<i class='mdi mdi-check-circle'></i>";
                            }

                            if ($d->details != "") {
                                foreach ($temp as $t) {
                                    $details .= Student::returnStudentName(
                                        $d->studentId
                                    ) . "'s transaction: " . self::moneySign($school) .
                                        number_format($t, 2) . "<br>";
                                }
                            } else {
                                $details = "NO PAYMENT DETAIL";
                            }

                            if (Student::returnStudentName($d->studentId) != "") {
                                $str .= "<tr>";
                                $str .= "<td class='$color'>$label " .
                                    Student::returnStudentName($d->studentId) .
                                    "</td>";
                                $str .= "<td class='$color'>$details</td>";
                                $str .= "<td class='$color'>" . self::moneySign($school) .
                                    number_format($bal, 2) . "</td>";
                                $str .= "<td class='$color'>$label " . self::moneySign($school) .
                                    number_format($d->paid, 2) . "<br>$tip</td>";
                                $str .= "<td>" . self::moneySign($school) . number_format($feeToPay, 2) . "</td>";
                                $str .= "<td class='$color'><a href='updatefee.php?studentId=$d->studentId&year=$d->academicYear&term=$d->term&class=$d->classId' class='btn btn-facebook'>Update</a></td>";
                                $str .= "</tr>";
                            }
                        }
                        $str .= "</tbody></table>";
                        echo $str;
                    } else {
                        echo "<h1 class='text-danger text-center'>No data retrieved :(</h1>";
                    }
                } else {
                    echo "<h1 class='text-danger text-center'>No data retrieved :(</h1>";
                }
            } catch (Exception $ex) {
                throw new Exception($ex->getMessage());
            }
        } else {
            echo "<h1 class='text-danger text-center'>No data retrieved <i class='mdi mdi-ghost-off mdi-spin'></i> (Fee is not set)</h1>";
        }
    }

    public static function moneySign($school)
    {
        $db = Database::getInstance();
        $db->query("SELECT `money_sign` FROM school WHERE schoolId=?");
        $db->bind(1, $school);
        $data = $db->single();
        return ($db->rowCount() > 0) ? $data->money_sign : "$";
    }


    public static function generateFee($classId, $year, $school)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM feerange WHERE year=? AND school=?");
        $db->bind(1, $year);
        $db->bind(2, $school);
        $data = $db->resultset();
        if ($db->rowCount() > 0) {
            if ($data != null) {
                foreach ($data as $d) {
                    $tempClasses = explode(",", $d->class);
                    if (in_array($classId, $tempClasses)) {
                        return $d->fee;
                    }
                }
            } else {
                return 0.00;
            }
        } else {
            return 0.00;
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    private static function insertFeeData($classId, $year, $term, $school)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT * FROM students WHERE class=? AND academicYear=? AND school=?"
        );
        $db->bind(1, $classId);
        $db->bind(2, $year);
        $db->bind(3, $school);
        $data = $db->resultset();
        if ($db->rowCount() > 0) {
            foreach ($data as $d) {
                $db->query(
                    "SELECT studentId FROM fees WHERE studentId=? AND academicYear=? AND term=?"
                );
                $db->bind(1, $d->studentId);
                $db->bind(2, $d->academicYear);
                $db->bind(3, $term);
                $db->execute();
                if ($db->rowCount() == 0) {
                    $db->query(
                        "INSERT INTO fees (studentId, academicYear,classId,paid,term,school) VALUES(?,?,?,?,?,?)"
                    );
                    $db->bind(1, $d->studentId);
                    $db->bind(2, $d->academicYear);
                    $db->bind(3, $d->class);
                    $db->bind(4, 0.00);
                    $db->bind(5, $term);
                    $db->bind(6, $school);
                    $db->execute();
                }
            }
        } else {
            throw new Exception(
                "No student found for <b>{" . Helper::classEncode($classId) .
                    "}</b>"
            );
        }
    }


    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function getFee($id, $year, $school, $term)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM fees WHERE studentId=? AND academicYear=? AND school=? AND term=?");
        $db->bind(1, $id);
        $db->bind(2, $year);
        $db->bind(3, $school);
        $db->bind(4, $term);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data;
        } else {
            throw new Exception();
        }
    }

    public static function updateFeePayment(
        $studentId,
        $term,
        $year,
        $amount,
        $school,
        $special,
        $fee
    ) {
        $db = Database::getInstance();
        $db->query(
            "SELECT * FROM fees WHERE studentId=? AND term=? AND academicYear=? AND school=?"
        );
        $db->bind(1, $studentId);
        $db->bind(2, $term);
        $db->bind(3, $year);
        $db->bind(4, $school);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            $detail = $data->details;
            if ($detail == "") {
                $detail = $amount;
            } else {
                $detail .= "," . $amount;
            }
            $amount += $data->paid;
            $db->query(
                "UPDATE fees SET details=?,paid=?,hasSpecial=?,feeToPay=? WHERE studentId=? AND term=? AND academicYear=? AND school=?"
            );
            $db->bind(1, $detail);
            $db->bind(2, $amount);
            $db->bind(3, $special);
            $db->bind(4, $fee);
            $db->bind(5, $studentId);
            $db->bind(6, $term);
            $db->bind(7, $year);
            $db->bind(8, $school);
            $db->execute();
        }
    }

    public static function subtractFeePayment(
        $studentId,
        $term,
        $year,
        $amount,
        $school,
        $special,
        $fee
    ) {
        $db = Database::getInstance();
        $db->query(
            "SELECT * FROM fees WHERE studentId=? AND term=? AND academicYear=? AND school=?"
        );
        $db->bind(1, $studentId);
        $db->bind(2, $term);
        $db->bind(3, $year);
        $db->bind(4, $school);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            $detail = $data->details;
            if ($detail == "") {
                $detail = $amount;
            } else {
                $detail .= "," . $amount;
            }
            $amount = $data->paid + $amount;

            $db->query(
                "UPDATE fees SET details=?,paid=?,hasSpecial=?,feeToPay=? WHERE studentId=? AND term=? AND academicYear=? AND school=?"
            );
            $db->bind(1, $detail);
            $db->bind(2, $amount);
            $db->bind(3, $special);
            $db->bind(4, $fee);
            $db->bind(5, $studentId);
            $db->bind(6, $term);
            $db->bind(7, $year);
            $db->bind(8, $school);
            $db->execute();
        }
    }


    public static function updateFeeRange($class, $id, $new, $school)
    {
        $db = Database::getInstance();
        $db->query("UPDATE feerange SET class=?,fee=? WHERE id=? AND school=?");
        $db->bind(1, $class);
        $db->bind(2, $new);
        $db->bind(3, $id);
        $db->bind(4, $school);
        return $db->execute();
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function getFeeR($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM feerange WHERE id=?");
        $db->bind(1, $id);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data;
        } else {
            throw new Exception();
        }
    }

    public static function getGradeTypes($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT grade_fields FROM subjects WHERE subjectId=?");
        $db->bind(1, $id);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->grade_fields;
        } else {
            return "";
        }
    }

    /**
     * Checks if a teacher is the class teacher of that specified class
     *
     * @param int $classId
     * @param int $teacherId
     * @param int $school
     * @return bool
     */
    public static function isATeacherInClass(
        $classId,
        $teacherId,
        $school
    ): bool {
        $db = Database::getInstance();
        $db->query("SELECT classTeacher FROM classes WHERE classId=? AND school=?")->bind(
            1,
            $classId
        )->bind(2, $school);
        $result = $db->single();
        if ($db->rowCount() > 0) {
            $teachersList = $result->classTeacher;
            $expandedList = explode(",", $teachersList);
            if (count($expandedList) > 0) {
                foreach ($expandedList as $list) {
                    if ($list == $teacherId) {
                        return TRUE;
                    }
                }
                return FALSE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public static function hasBeenAssignedAClass(int $teacherId, int $school, string $role): bool
    {
        if ($role == "Administrator" || $role == "Super-Admin") {
            return TRUE;
        }

        $db = Database::getInstance();
        $results = $db->query("SELECT classTeacher FROM classes WHERE school=?")
            ->bind(1, $school)
            ->resultset();
        if ($db->rowCount() > 0) {
            foreach ($results as $result) {
                $teachersList = $result->classTeacher;
                $expandedList = explode(",", $teachersList);
                if (in_array($teacherId, $expandedList)) {
                    return TRUE;
                }
            }
            return FALSE;
        }
        return FALSE;
    }

    public static function addBillingInfo($paymentId, $frequency, $currency, $amount, $cycles, $tax, $billingInterval, $status, $payerEmail, $username, $payerId, $startDate)
    {
        $db = Database::getInstance();
        $db->query("INSERT INTO billings (paymentId,frequency,currency,amount,cycles,tax,billingInterval,status,payerEmail,username,payerId,startDate) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
        $db->bind(1, $paymentId)
            ->bind(2, $frequency)
            ->bind(3, $currency)
            ->bind(4, $amount)
            ->bind(5, $cycles)
            ->bind(6, $tax)
            ->bind(7, $billingInterval)
            ->bind(8, $status)
            ->bind(9, $payerEmail)
            ->bind(10, $username)
            ->bind(11, $payerId)
            ->bind(12, $startDate);
        if ($db->execute()) {
            return $db->lastInsertId();
        } else {
            return false;
        }
    }

    private static function getPaymentLink($school)
    {
        $db = Database::getInstance();
        $db->query("SELECT payment FROM school WHERE schoolId=?");
        $db->bind(1, $school);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            $id = $data->payment;

            return $id;
        }
        return null;
    }

    public static function activateSchool($school, $paymentId)
    {
        $db = Database::getInstance();
        $db->query("UPDATE school SET isActivated=?, payment=? WHERE schoolId=?");
        $db->bind(1, 1);
        $db->bind(2, $paymentId);
        $db->bind(3, $school);
        return $db->execute();
    }

    public static function deactivateSchool($school)
    {
        $id = self::getPaymentLink($school);
        if ($id != null) {
            $db = Database::getInstance();
            $db->query("UPDATE school SET isActivated=? WHERE schoolId=?");
            $db->bind(1, 0);
            $db->bind(2, $school);
            if ($db->execute()) {
                $db->query("UPDATE billings SET status=? WHERE id=?");
                $db->bind(1, "Inactive");
                $db->bind(2, $id);
                return $db->execute();
            }
        }
    }

    public static function getPaymentId($school)
    {
        $db = Database::getInstance();
        $db->query("SELECT payment FROM school WHERE schoolId=?");
        $db->bind(1, $school);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            $id = $data->payment;
            $db->query("SELECT * FROM billings WHERE id=?")->bind(1, $id);
            $result = $db->single();
            if ($db->rowCount() > 0) {
                return $result;
            }
            return null;
        }
        return null;
    }

    public static function getPaymentDate($paymentId)
    {
        $db = Database::getInstance();
        $db->query("SELECT startDate FROM billings WHERE paymentId=?");
        $db->bind(1, $paymentId);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->startDate;
        }
        return null;
    }

    public static function isValidDate($date, $firstMonth, $payment)
    {
        if ($date) {
            $days = 0;
            $today = date('Y-m-d H:i:s');
            $nowObj = new \DateTime($today);
            if ($firstMonth) {
                $startDateObj = new \DateTime($date);
                $days = $startDateObj->diff($nowObj)->days;
                return $days <= FREE_TRIAL_DAYS;
            } else {
                $startDateObj = new DateTime($payment->startDate);
                $endDateObj = new DateTime($payment->endDate);
                $daysNowSinceActivated = $startDateObj->diff($endDateObj)->days;
                $daysNowSinceStart = $startDateObj->diff($nowObj)->days;
                return $daysNowSinceActivated >= $daysNowSinceStart;
            }
        }
        return FALSE;
    }

    public static function isFirstMonth($schoolId)
    {
        $db = Database::getInstance();
        $db->query("SELECT usingFirstTimePromo FROM school WHERE schoolId=?");
        $db->bind(1, $schoolId);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->usingFirstTimePromo == 1;
        }
        return FALSE;
    }


    public static function getDebtors($class, $year, $term, $school)
    {
        $debtors = [];
        $toPay = self::generateFee($class, $year, $school);
        $db = Database::getInstance();
        $db->query("SELECT studentId FROM fees WHERE paid < ? AND classId=? AND term=? AND academicYear=? AND school=? AND hasSpecial=?")
            ->bind(1, $toPay)
            ->bind(2, $class)
            ->bind(3, $term)
            ->bind(4, $year)
            ->bind(5, $school)
            ->bind(6, 0);
        $results = $db->resultset();
        if ($db->rowCount() > 0) {
            foreach ($results as $result) {
                $debtors[] = $result->studentId;
            }
        }

        // get special cases 
        $db->query("SELECT studentId, paid, feeToPay FROM fees WHERE classId=? AND term=? AND academicYear=? AND school=? AND hasSpecial=?")
            ->bind(1, $class)
            ->bind(2, $term)
            ->bind(3, $year)
            ->bind(4, $school)
            ->bind(5, 1);
        $results = $db->resultset();
        if ($db->rowCount() > 0) {
            foreach ($results as $result) {
                if ($result->paid < $result->feeToPay) {
                    $debtors[] = $result->studentId;
                }
            }
        }

        return (!empty($debtors)) ? $debtors : null;
    }

    public static function appendNoticeDate($class, $school)
    {
        $db = Database::getInstance();
        $db->query("UPDATE classes SET notice_sent =? WHERE classId=? AND school=?");
        $date = date("d-m-Y h:i:s");
        $db->bind(1, $date);
        $db->bind(2, $class);
        $db->bind(3, $school);
        $db->execute();
    }

    public static function getNoticeDate($class, $school)
    {
        $db = Database::getInstance();
        $db->query("SELECT notice_sent FROM classes WHERE classId=? AND school=?");
        $db->bind(1, $class);
        $db->bind(2, $school);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->notice_sent;
        }
        return "";
    }

    public static function addEvent($title, $description, $start, $end, $long, $occur, $school, $year)
    {
        $db = Database::getInstance();
        $db->query("INSERT INTO events (title,description,start,end,interval_days,days,school,year) VALUES(?,?,?,?,?,?,?,?)");
        $db->bind(1, $title);
        $db->bind(2, $description);
        $db->bind(3, $start);
        $db->bind(4, $end);
        $db->bind(5, $long);
        $db->bind(6, $occur);
        $db->bind(7, $school);
        $db->bind(8, $year);
        $db->execute();
    }

    public static function getEvents($school, $year)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM events WHERE school=? AND year=?");
        $db->bind(1, $school);
        $db->bind(2, $year);
        $data = $db->resultset();
        if ($db->rowCount() > 0) {
            return $data;
        } else {
            return NULL;
        }
    }

    public static function getStudentIds($class)
    {
        $db = Database::getInstance();
        $db->query("SELECT studentId FROM students WHERE class=?");
        $db->bind(1, $class);
        $results = $db->resultset();
        if ($db->rowCount() > 0) {
            return $results;
        } else {
            return NULL;
        }
    }

    public static function getLevelsForSubject($subId)
    {
        $db = Database::getInstance();
        $db->query("SELECT levels FROM subjects WHERE subjectId=?");
        $db->bind(1, $subId);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            if ($data->levels != "") {
                return explode(",", $data->levels);
            }
            return NULL;
        }
        return NULL;
    }

    public static function getClassLevel($class)
    {
        $db  = Database::getInstance();
        $db->query("SELECT levels FROM classes WHERE classId=?");
        $db->bind(1, $class);
        $data = $db->single();
        return $db->rowCount() > 0 ? $data->levels : NULL;
    }

    public static function getStudentEmails($class)
    {
        $db = Database::getInstance();
        $db->query("SELECT email FROM students WHERE class=?");
        $db->bind(1, $class);
        $results = $db->resultset();
        if ($db->rowCount() > 0) {
            return $results;
        } else {
            return NULL;
        }
    }

    public static function isAssessmentValid($date, $id)
    {
        $now = date("Y-m-d");
        $dateLib = new DateLib();
        $length = WorkshopAssessment::getDueLength($id);
        if ($length) {
            $interval = $dateLib->interval($date, $now);
            if ($interval->days > $length->dueLength) {
                return FALSE;
            }
        }
        return TRUE;
    }

    public static function canChooseSubject($class)
    {
        $db = Database::getInstance();
        $db->query("SELECT choose FROM classes WHERE classId = ?");
        $db->bind(1, $class);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->choose == 1;
        }
        return FALSE;
    }

    public static function getAssessmentGradeList($studentId, $subject, $term, $academicYear, $school)
    {
        $db = Database::getInstance();
        $db->query("SELECT assessment_list FROM results WHERE student_id=? AND subject_id=? AND term=? AND academicYear=? AND school=?");
        $db->bind(1, $studentId);
        $db->bind(2, $subject);
        $db->bind(3, $term);
        $db->bind(4, $academicYear);
        $db->bind(5, $school);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->assessment_list;
        }
        return NULL;
    }

    public static function markAsGraded($assessment_id, $student)
    {
        $db = Database::getInstance();
        $db->query("UPDATE submissions SET has_been_graded=1 WHERE assessment=? AND student=?");
        $db->bind(1, $assessment_id);
        $db->bind(2, $student);
        $db->execute();
    }
}
