<?php

namespace App\School;

use App\Database\Database;
use App\Core\Helper;
use Exception;

class Subject
{

    public static int $subcount = 0;
    private $subjectId;
    private $subject;

    public function __construct($subject, $subjectId = 0)
    {
        $this->subject = $subject;
        $this->subjectId = $subjectId;
    }

    public function getSubjectId()
    {
        return $this->subjectId;
    }

    public function getSubjectIn()
    {
        return $this->subject;
    }

    public function setSubjectId($subjectId)
    {
        $this->subjectId = $subjectId;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function __toString()
    {
        return $this->getSubjectIn();
    }

    public static function loadSubjectsF($school)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM subjects WHERE school=? ORDER BY subject")->bind(
            1,
            $school
        );
        $data = $db->resultset();
        self::$subcount = $db->rowCount();
        if ($db->rowCount() > 0) {
            return $data;
        } else
            throw new \Exception("No record found");
    }

    public static function getSubjectList($list)
    {
        $db = Database::getInstance();
        $list = explode(",", $list);
        $temp = "";
        if ($list != null) {
            foreach ($list as $l) {
                $db->query("SELECT * FROM subjects WHERE subjectId=?");
                $db->bind(1, $l);
                $data = $db->single();
                if ($db->rowCount() > 0) {
                    $temp .= $data->subject . ",";
                }
            }
        }
        return Helper::makeList($temp);
    }

    /**
     *
     * @param Subject $sub
     * @return boolean
     * @throws Exception
     * @throws PDOException
     */
    public static function addSubject(
        $sub,
        $teachers,
        $pass,
        $final,
        $grades,
        $school,
        $assessment,
        $levels
    ) {
        $grades = Helper::makeList($grades);
        $db = Database::getInstance();
        $db->query("SELECT subject FROM subjects WHERE subject=? AND school=?");
        $db->bind(1, $sub->getSubjectIn());
        $db->bind(2, $school);
        $db->execute();
        if ($db->rowCount() == 0) {
            $db->query(
                "INSERT INTO subjects (subject, teacherId, passGrade, finalGrade,grade_fields,school,assessment,levels) VALUES(?,?,?,?,?,?,?,?)"
            );
            $db->bind(1, $sub->getSubjectIn());
            $db->bind(2, $teachers);
            $db->bind(3, $pass);
            $db->bind(4, $final);
            $db->bind(5, $grades);
            $db->bind(6, $school);
            $db->bind(7, $assessment);
            $db->bind(8, $levels);
            $db->execute();
            if ($db->rowCount() > 0) {
                return true;
            } else {
                throw new Exception("Can't process this request");
            }
        } else {
            throw new Exception("Subject already exist");
        }
    }

    public static function updateSubject(
        $sub,
        $teachers,
        $pass,
        $final,
        $id,
        $opt,
        $assessment,
        $levels
    ) {
        $opt = Helper::makeList($opt);
        $db = Database::getInstance();
        $db->query(
            "UPDATE subjects SET subject=?,teacherId=?,passGrade=?,finalGrade=?,grade_fields=?,assessment=?,levels=? WHERE subjectId=?"
        );
        $db->bind(1, $sub);
        $db->bind(2, $teachers);
        $db->bind(3, $pass);
        $db->bind(4, $final);
        $db->bind(5, $opt);
        $db->bind(6, $assessment);
        $db->bind(7, $levels);
        $db->bind(8, $id);
        $db->execute();
        if ($db->rowCount() > 0) {
            return true;
        } else {
            throw new Exception(
                "Can't process this request.<br><i class='text-small'>Seems like you didn't change anything</i>."
            );
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function loadSubjects($school)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM subjects WHERE school=? ORDER BY subject");
        $db->bind(1, $school);
        $data = $db->resultset();
        if ($db->rowCount() > 0) {
            return $data;
        } else {
            throw new Exception("No record found");
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function returnSubject($classId, $school)
    {
        try {
            $list = self::getSubjectsByClass($classId, $school);
        } catch (Exception $e) {
        }
        $db = Database::getInstance();
        $subList = explode(",", $list);
        $data = [];
        if ($subList != null) {
            foreach ($subList as $sub) {
                $db->query(
                    "SELECT * FROM subjects WHERE subjectId=? AND school=?"
                );
                $db->bind(1, $sub);
                $db->bind(2, $school);
                $d = $db->single();
                if ($db->rowCount() > 0) {
                    $data[] = $d;
                }
            }
        }
        if ($data != null) {
            return $data;
        } else {
            throw new Exception("Nothing here");
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function getSubjectsByClass($id, $school)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT classSubjects FROM classes WHERE classId=? AND school=?"
        );
        $db->bind(1, $id);
        $db->bind(2, $school);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->classSubjects;
        } else {
            throw new Exception();
        }
    }



    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function getSubject($id, $school)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM subjects WHERE subjectId=? AND school=?");
        $db->bind(1, $id);
        $db->bind(2, $school);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data;
        } else {
            return NULL;
        }
    }

    /**
     *
     * @noinspection PhpUnhandledExceptionInspection
     */
    public static function deleteSubject($id, $acadYear)
    {
        $db = Database::getInstance();
        $db->query(
            "SELECT * FROM results WHERE subject_id=? AND academicYear=?"
        );
        $db->bind(1, $id);
        $db->bind(2, $acadYear);
        $db->execute();
        if ($db->rowCount() < 1) {
            $db->query("DELETE FROM subjects WHERE subjectId=?");
            $db->bind(1, $id);
            $db->execute();
        } else {
            throw new Exception(
                "You cannot delete this subject. A student's grade is depending on it."
            );
        }
    }

    public static function getMySubjectsInClass($class, $teacher, $school)
    {
        $mySubjects = self::getMySubjects($teacher, $school);
        $classSubjects = self::getSubjectsByClass($class, $school);
        $returnedSubjects = [];
        $classSubjects = explode(",", $classSubjects);

        if ($mySubjects) {
            foreach ($mySubjects as $mySub) {
                if (in_array($mySub->subjectId, $classSubjects)) {
                    $returnedSubjects[] = $mySub;
                }
            }
        }

        return $returnedSubjects;
    }

    public static function getMySubjects($teacher, $school)
    {
        $subjectLists = [];
        $db = Database::getInstance();
        $db->query("SELECT * FROM subjects WHERE school=?")->bind(
            1,
            $school
        );
        $results = $db->resultset();
        if ($db->rowCount() > 0) {
            foreach ($results as $result) {
                $teachersList = $result->teacherId;
                $expandedList = explode(",", $teachersList);
                if (count($expandedList) > 0) {
                    foreach ($expandedList as $list) {
                        if ($list == $teacher) {
                            $subjectLists[] = $result;
                        }
                    }
                } else {
                    return NULL;
                }
            }
            return $subjectLists;
        } else {
            return NULL;
        }
    }


    public static function getMySubjectsByLevel($teacher, $school, $class)
    {
        $mySubjects = self::getMySubjects($teacher, $school);
        $classSubs = self::getSubjectsByClass($class, $school);
        $classLevel = Helper::getClassLevel($class);
        $subjectsArray = [];
        if ($mySubjects && $classLevel && $classSubs) {
            $classSubs = explode(",", $classSubs);
            foreach ($mySubjects as $subject) {
                $levels = Helper::getLevelsForSubject($subject->subjectId);
                if ($levels) {
                    if (in_array($classLevel, $levels)) {
                        if (in_array($subject->subjectId, $classSubs)) {
                            $subjectsArray[] = $subject->subjectId;
                        }
                    }
                }
            }
            return $subjectsArray;
        }
        return NULL;
    }


    public static function getSubjectsByLevel($school, $class)
    {
        $mySubjects = self::getSubjectsByClass($class, $school);
        $classLevel = Helper::getClassLevel($class);
        $subjectsArray = [];
        if ($mySubjects && $classLevel) {
            $mySubjects = explode(",", $mySubjects);
            foreach ($mySubjects as $subject) {
                $levels = Helper::getLevelsForSubject($subject);
                if ($levels) {
                    if (in_array($classLevel, $levels) || (in_array('ALL', $levels))) {
                        $subjectsArray[] = $subject;
                    }
                }
            }
            return $subjectsArray;
        }
        return NULL;
    }

    public static function getSubjectsByLevelExplicit($school, $class)
    {
        $mySubjects = self::loadSubjectsF($school);
        $subs = [];
        if ($mySubjects) {
            foreach ($mySubjects as $sub) {
                $subs[] = $sub->subjectId;
            }
        }
        $mySubjects = implode(",", $subs);
        $classLevel = Helper::getClassLevel($class);
        $subjectsArray = [];
        if ($mySubjects && $classLevel) {
            $mySubjects = explode(",", $mySubjects);
            foreach ($mySubjects as $subject) {
                $levels = Helper::getLevelsForSubject($subject);
                if ($levels) {
                    if (in_array($classLevel, $levels) || (in_array('ALL', $levels))) {
                        $subjectsArray[] = $subject;
                    }
                }
            }
            return $subjectsArray;
        }
        return NULL;
    }


    public static function isSubjectInClass(
        int $subjectId,
        int $classId,
        int $school
    ): bool {
        $db = Database::getInstance();
        $db->query(
            "SELECT classSubjects FROM classes WHERE school=? AND classId=?"
        )
            ->bind(1, $school)
            ->bind(2, $classId);
        $result = $db->single();
        if ($db->rowCount() > 0) {
            $subjectsList = $result->classSubjects;
            $expandedList = explode(',', $subjectsList);
            if (count($expandedList) > 0) {
                foreach ($expandedList as $list) {
                    if ($subjectId == $list) {
                        return TRUE;
                    }
                }
                return FALSE;
            }
            return FALSE;
        }
        return FALSE;
    }


    public static function hasBeenAssignedASubject(int $teacherId, int $school): bool
    {
        $db = Database::getInstance();
        $result = $db->query("SELECT teacherId FROM subjects WHERE school=?")
            ->bind(1, $school)
            ->resultset();
        if ($db->rowCount() > 0) {
            foreach ($result as $res) {
                $teachersList = $res->teacherId;
                $expandedList = explode(",", $teachersList);
                if (count($expandedList) > 0) {
                    if (in_array($teacherId, $expandedList)) {
                        return TRUE;
                    }
                }
            }
            return FALSE;
        }
        return FALSE;
    }

    public static function getName($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT subject FROM subjects WHERE subjectId=?");
        $db->bind(1, $id);
        $data = $db->single();
        if ($db->rowCount() > 0)
            return $data->subject;
        return "Unknown";
    }
}
