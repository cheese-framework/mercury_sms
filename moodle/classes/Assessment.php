<?php

use App\Core\Helper;
use App\Database\Database;
use App\Database\Finder;
use App\Database\Paginate;
use App\Extra\Assessment\WorkshopAssessment;
use App\Helper\DateLib;
use App\School\Student;
use App\School\Subject;

class Assessment
{

    public static function getSubmissions($assessmentId, $limit, $lines)
    {
        $submissions = [];
        $sql = Finder::select('submissions', 'assessment, student, class, subject, term, has_been_graded,year')
            ->where('assessment=:assessmentid')->order('student', 'ASC');
        $paginate = new Paginate($sql::getSql(), $limit, $lines);
        foreach ($paginate->paginate(Database::getInstance(), [':assessmentid' => $assessmentId]) as $row) {
            $submissions[] = $row;
        }

        return $submissions;
    }


    public static function getPendingAssessments($id, $class)
    {
        // check if the class can choose subject
        $canChooseSubject = Helper::canChooseSubject($class);
        if ($canChooseSubject) {
            $mySubjects = Student::getMySubjects($id);
            if ($mySubjects) {
                $db = Database::getInstance();
                $db->query("SELECT id,due,subject FROM assessments WHERE class=?");
                $db->bind(1, $class);
                $results = $db->resultset();
                $validAssessments = [];
                if ($db->rowCount() > 0) {
                    foreach ($results as $data) {
                        if (in_array($data->subject, $mySubjects)) {
                            if (!DateLib::isInvert(date("Y-m-d"), $data->due)) {
                                $validAssessments[] = $data->id;
                            }
                        }
                    }
                    $count = count($validAssessments);
                    // check submission
                    foreach ($validAssessments as $valid) {
                        $db->query("SELECT id FROM submissions WHERE assessment=? AND student=?");
                        $db->bind(1, $valid);
                        $db->bind(2, $id);
                        $db->execute();
                        $count -= $db->rowCount();
                    }
                    return $count;
                }
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id,due FROM assessments WHERE class=?");
            $db->bind(1, $class);
            $results = $db->resultset();
            $validAssessments = [];
            if ($db->rowCount() > 0) {
                foreach ($results as $data) {
                    if (!DateLib::isInvert(date("Y-m-d"), $data->due)) {
                        $validAssessments[] = $data->id;
                    }
                }
                $count = count($validAssessments);
                // check submission
                foreach ($validAssessments as $valid) {
                    $db->query("SELECT id FROM submissions WHERE assessment=? AND student=?");
                    $db->bind(1, $valid);
                    $db->bind(2, $id);
                    $db->execute();
                    $count -= $db->rowCount();
                }
                return $count;
            }
        }
        return 0;
    }

    public static function getValidAssessments($id, $class)
    {
        // check if the class can choose subject
        $canChooseSubject = Helper::canChooseSubject($class);

        if ($canChooseSubject) {
            $mySubjects = Student::getMySubjects($id);
            if ($mySubjects) {
                $db = Database::getInstance();
                $db->query("SELECT id,due,subject FROM assessments WHERE class=?");
                $db->bind(1, $class);
                $results = $db->resultset();
                $validAssessments = [];
                if ($db->rowCount() > 0) {
                    foreach ($results as $data) {
                        if (in_array($data->subject, $mySubjects)) {
                            if (!DateLib::isInvert(date("Y-m-d"), $data->due)) {
                                $validAssessments[] = $data->id;
                            }
                        }
                    }
                    return count($validAssessments);
                }
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id,due FROM assessments WHERE class=?");
            $db->bind(1, $class);
            $results = $db->resultset();
            $validAssessments = [];
            if ($db->rowCount() > 0) {
                foreach ($results as $data) {
                    if (!DateLib::isInvert(date("Y-m-d"), $data->due)) {
                        $validAssessments[] = $data->id;
                    }
                }
                return count($validAssessments);
            }
        }


        return 0;
    }


    public static function getExpiredAssessments($id, $class)
    {
        // check if the class can choose subject
        $canChooseSubject = Helper::canChooseSubject($class);

        if ($canChooseSubject) {
            $mySubjects = Student::getMySubjects($id);
            if ($mySubjects) {
                $db = Database::getInstance();
                $db->query("SELECT id,due,subject FROM assessments WHERE class=?");
                $db->bind(1, $class);
                $results = $db->resultset();
                $validAssessments = [];
                if ($db->rowCount() > 0) {
                    foreach ($results as $data) {
                        if (in_array($data->subject, $mySubjects)) {
                            if (DateLib::isInvert(date("Y-m-d"), $data->due)) {
                                $validAssessments[] = $data->id;
                            }
                        }
                    }
                    return count($validAssessments);
                }
            }
        } else {
            $db = Database::getInstance();
            $db->query("SELECT id,due FROM assessments WHERE class=?");
            $db->bind(1, $class);
            $results = $db->resultset();
            $validAssessments = [];
            if ($db->rowCount() > 0) {
                foreach ($results as $data) {
                    if (DateLib::isInvert(date("Y-m-d"), $data->due)) {
                        $validAssessments[] = $data->id;
                    }
                }
                return count($validAssessments);
            }
        }
        return 0;
    }



    public static function getAssessmentForStudent($school, $studentId, $class, $limit, $lines)
    {
        $mySubjects = Student::getMySubjects($studentId);
        $coreSubjects = Subject::getSubjectsByClass($class, $school);
        if ($coreSubjects) {
            $coreSubjects = explode(",", $coreSubjects);
        } else {
            return;
        }

        if (!$mySubjects) {
            $mySubjects = [];
        }
        if (!$coreSubjects) {
            $coreSubjects = [];
        }

        $assessments = NULL;

        $sql = Finder::select('assessments')
            ->where('school=:school AND class=:class')->order('due', 'DESC');
        $paginate = new Paginate($sql::getSql(), $limit, $lines);
        foreach ($paginate->paginate(Database::getInstance(), [':school' => $school, ':class' => $class]) as $row) {

            if (in_array($row->subject, $coreSubjects)) {
                $assessments[] = $row;
            }

            if (in_array($row->subject, $mySubjects)) {
                $assessments[] = $row;
            }
        }
        return $assessments;
    }


    public static function submitAssessment($text, $file, $student, $class, $school, $assessmentId, $year, $subject, $term)
    {
        $db = Database::getInstance();
        $db->query("INSERT INTO submissions (assessment, school, student, class, details, file, year, subject, term) VALUES(?, ?, ?, ?, ?, ?, ?,?,?)");
        $db->bind(1, $assessmentId);
        $db->bind(2, $school);
        $db->bind(3, $student);
        $db->bind(4, $class);
        $db->bind(5, $text);
        $db->bind(6, $file);
        $db->bind(7, $year);
        $db->bind(8, $subject);
        $db->bind(9, $term);
        $db->execute();
        if ($db->rowCount() > 0) {
            $db->query("SELECT submitted FROM assessments WHERE id=?");
            $db->bind(1, $assessmentId);
            $data = $db->single();
            if ($db->rowCount() > 0) {
                $submitted = $data->submitted;
                $submitted++;
                $db->query("UPDATE assessments SET submitted =? WHERE id=?");
                $db->bind(1, $submitted);
                $db->bind(2, $assessmentId);
                $db->execute();
            }
            return TRUE;
        }
        return FALSE;
    }


    public static function updateSubmission($submission, $file, $student, $assessmentId)
    {
        $db = Database::getInstance();
        $db->query("UPDATE submissions SET details=?, file=? WHERE student=? AND assessment=?");
        $db->bind(1, $submission);
        $db->bind(2, $file);
        $db->bind(3, $student);
        $db->bind(4, $assessmentId);
        return $db->execute();
    }

    public static function deleteSubmission($id, $assessmentId)
    {
        $db = Database::getInstance();
        $db->query("DELETE FROM submissions WHERE id=?");
        $db->bind(1, $id);
        if ($db->execute()) {
            self::reduceSubmissionCount($assessmentId);
            return TRUE;
        }
        return FALSE;
    }

    public static function hasSubmitted($student, $assessmentId)
    {
        $sql = Finder::select('submissions', 'id')
            ::where('student=:student AND assessment=:assessmentId');
        $query = $sql::getSql();
        $db = Database::getInstance();
        $db->query($query);
        $db->bind(':student', $student);
        $db->bind(':assessmentId', $assessmentId);
        $db->execute();
        return $db->rowCount() > 0;
    }

    public static function getSubmittedAssessment($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM submissions WHERE assessment=?");
        $db->bind(1, $id);
        $data = $db->single();
        if ($db->rowCount() > 0)
            return $data;
        return NULL;
    }

    private static function reduceSubmissionCount($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT submitted FROM assessments WHERE id=?");
        $db->bind(1, $id);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            $submitted = $data->submitted;
            if ($submitted >= 1) {
                $submitted--;
            }
            $db->query("UPDATE assessments SET submitted =? WHERE id=?");
            $db->bind(1, $submitted);
            $db->bind(2, $id);
            $db->execute();
        }
    }

    public static function getAssessmentText($id)
    {
        $db = Database::getInstance();
        $db->query("SELECT details FROM assessments WHERE id=?");
        $db->bind(1, $id);
        $record = $db->single();
        if ($db->rowCount() > 0) return $record->details;
        return NULL;
    }
}
