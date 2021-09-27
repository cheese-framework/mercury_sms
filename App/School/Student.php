<?php

namespace App\School;

use App\Database\Database;
use Exception;

class Student
{

    private $studentId;
    private $studentFullName;
    private $studentClass;
    private $academicYear;
    private $studentGender;

    public function __construct($studentFullName, $studentClass, $academicYear, $gender, $studentId = 0)
    {
        $this->studentFullName = $studentFullName;
        $this->studentClass = $studentClass;
        $this->academicYear = $academicYear;
        $this->studentId = $studentId;
        $this->studentGender = $gender;
    }

    public function getStudentId()
    {
        return $this->studentId;
    }

    public function getStudentFullName()
    {
        return $this->studentFullName;
    }

    public function getStudentClass()
    {
        return $this->studentClass;
    }

    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    public function getStudentGender()
    {
        return $this->studentGender;
    }

    public function setStudentId($studentId): void
    {
        $this->studentId = $studentId;
    }

    public function setStudentFullName($studentFullName): void
    {
        $this->studentFullName = $studentFullName;
    }

    public function setStudentClass($studentClass): void
    {
        $this->studentClass = $studentClass;
    }

    public function setAcademicYear($academicYear): void
    {
        $this->academicYear = $academicYear;
    }

    public function setStudentGender($studentGender): void
    {
        $this->studentGender = $studentGender;
    }

    public function __toString()
    {
        return $this->studentFirstName . " " . $this->studentLastName;
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function getNumStudents($classId, $year)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM students WHERE class=? AND academicYear=?");
        $db->bind(1, $classId);
        $db->bind(2, $year);
        $db->execute();
        return $db->rowCount();
    }

    public static function addStudent(
        $name,
        $class,
        $year,
        $gender,
        $dob,
        $phone,
        $address,
        $admissionno,
        $blood,
        $parphone,
        $emergcon,
        $medical,
        $school,
        $paremail,
        $email,
        $password
    ) {
        $db = Database::getInstance();
        $db->query(
            "SELECT fullname FROM students WHERE fullname=? AND academicYear=? AND class=? AND school=? AND email=?"
        );
        $db->bind(1, $name);
        $db->bind(2, $year);
        $db->bind(3, $class);
        $db->bind(4, $school);
        $db->bind(5, $email);
        $db->execute();
        if ($db->rowCount() > 0) {
            throw new Exception("Student already in class");
        } else {
            $db->query(
                "INSERT INTO students (fullname,class,academicYear,gender,dob,phone,address,admissionno,bloodgroup,parphone,emergcon,medical,school,paremail,email,password) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
            );
            $db->bind(1, $name);
            $db->bind(2, $class);
            $db->bind(3, $year);
            $db->bind(4, $gender);
            $db->bind(5, $dob);
            $db->bind(6, $phone);
            $db->bind(7, $address);
            $db->bind(8, $admissionno);
            $db->bind(9, $blood);
            $db->bind(10, $parphone);
            $db->bind(11, $emergcon);
            $db->bind(12, $medical);
            $db->bind(13, $school);
            $db->bind(14, $paremail);
            $db->bind(15, $email);
            $db->bind(16, $password);
            $db->execute();
            return ($db->rowCount() > 0);
        }
    }


    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function updateStudent(
        $name,
        $class,
        $gender,
        $dob,
        $phone,
        $address,
        $admissionno,
        $blood,
        $parphone,
        $emergcon,
        $medical,
        $id,
        $email,
        $sEmail,
        $password
    ) {
        $db = Database::getInstance();
        $db->query(
            "UPDATE students SET fullname=?,class=?,gender=?,dob=?,phone=?,address=?,admissionno=?,bloodgroup=?,parphone=?,emergcon=?,medical=?,paremail=?,email=?,password=? WHERE studentId=?"
        );
        $db->bind(1, $name);
        $db->bind(2, $class);
        $db->bind(3, $gender);
        $db->bind(4, $dob);
        $db->bind(5, $phone);
        $db->bind(6, $address);
        $db->bind(7, $admissionno);
        $db->bind(8, $blood);
        $db->bind(9, $parphone);
        $db->bind(10, $emergcon);
        $db->bind(11, $medical);
        $db->bind(12, $email);
        $db->bind(13, $sEmail);
        $db->bind(14, $password);
        $db->bind(15, $id);
        $db->execute();
        return ($db->rowCount() > 0);
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function getMyStudents($classId, $year)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT * FROM students WHERE class=? AND academicYear=? ORDER BY  fullname ASC"
        );
        $db->bind(1, $classId);
        $db->bind(2, $year);
        $result = $db->resultset();
        $records = [];
        if (!empty($result)) {
            foreach ($result as $data) {
                $temp = new Student(
                    $data->fullname,
                    $data->class,
                    $data->academicYear,
                    $data->gender,
                    $data->studentId
                );
                $records[] = $temp;
            }
            return $records;
        } else {
            throw new Exception("No record found!");
        }
    }

    public static function getAllStudents()
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM academic_year ORDER BY academicYearId DESC");
        $rec = $db->single();
        if (!empty($rec)) {
            $year = $rec->academicYear;
            $db->query("SELECT * FROM students WHERE academicYear=?");
            $db->bind(1, $year);
            $db->execute();
            return $db->rowCount();
        } else {
            return 0;
        }
    }

    public static function getStudents($classId, $school)
    {
        $db = Database::getInstance();
        $year = AcademicYear::getAcademicYearId(
            AcademicYear::getCurrentYear($school),
            $school
        );
        $db->query(
            "SELECT * FROM students WHERE class=? AND academicYear = ? ORDER BY fullname"
        );
        $db->bind(1, $classId);
        $db->bind(2, $year);
        $data = $db->resultset();
        if ($db->rowCount() > 0) {
            return $data;
        } else {
            return null;
        }
    }


    public static function getFullName($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT fullname FROM students WHERE studentId=?");
        $db->bind(1, $id);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->fullname;
        } else {
            return "";
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function getNumGender($classId, $year)
    {
        $data = [];
        $db = Database::getInstance();
        if ($classId > 0) {
            $db->query(
                "SELECT fullname FROM students WHERE gender='F' AND academicYear=? AND class=?"
            );
            $db->bind(1, $year);
            $db->bind(2, $classId);
            $db->execute();
            $data["female"] = $db->rowCount();

            $db->query(
                "SELECT fullname FROM students WHERE gender='M' AND academicYear=? AND class=?"
            );
            $db->bind(1, $year);
            $db->bind(2, $classId);
            $db->execute();
            $data["male"] = $db->rowCount();
        } else {
            $db->query(
                "SELECT fullname FROM students WHERE gender='F' AND academicYear=?"
            );
            $db->bind(1, $year);
            $db->execute();
            $data["female"] = $db->rowCount();

            $db->query(
                "SELECT fullname FROM students WHERE gender='M' AND academicYear=?"
            );
            $db->bind(1, $year);
            $db->execute();
            $data["male"] = $db->rowCount();
        }
        return $data;
    }


    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function deleteStudent($id, $school)
    {
        $db = Database::getInstance();
        $db->query("DELETE FROM totals WHERE studentId=?");
        $db->bind(1, $id);
        $db->execute();
        $db->query("SELECT * FROM results WHERE student_id=?");
        $db->bind(1, $id);
        $data = $db->resultset();
        if ($data != null) {
            foreach ($data as $d) {
                $db->query("DELETE FROM results WHERE resultId=?");
                $db->bind(1, $d->resultId);
                $db->execute();
            }
        }

        $db->query("DELETE FROM students WHERE studentId=? AND school=?");
        $db->bind(1, $id)->bind(2, $school);
        $db->execute();
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function returnStudentName($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT fullname FROM students WHERE studentId=?");
        $db->bind(1, $id);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->fullname;
        }
        return "";
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function getStudentDetails($id, $school)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM students WHERE studentId=? AND school=?");
        $db->bind(1, $id)->bind(2, $school);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data;
        } else {
            throw new Exception("NO RECORD FOUND");
        }
    }


    public static function getMySubjects($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT mysubjects FROM students WHERE studentId=? OR admissionno=?");
        $db->bind(1, $id);
        $db->bind(2, $id);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return ($data->mysubjects != NULL) ? array_unique(explode(",", $data->mysubjects)) : NULL;
        }
        return NULL;
    }

    public static function addElectives($student, $electives)
    {
        $db = Database::getInstance();
        $db->query("UPDATE students SET mysubjects=? WHERE admissionno=?");
        $db->bind(1, $electives);
        $db->bind(2, $student);
        // $db->bind(3, $student);
        return $db->execute();
    }
}
