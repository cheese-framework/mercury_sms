<?php


namespace App\Extra;

use App\Entity\Base;

class SchemeOfWork extends Base
{
    protected $grade = '';
    protected $subject = '';
    protected $topic = '';
    protected $duration = '';
    protected $school = 0;
    protected $content = '';
    protected $objective = '';
    protected $methodology = '';
    protected $reference = '';
    protected $assessment = '';
    protected $remark = '';
    protected $datecreated = '';
    protected $academicYear = '';
    const TABLE_NAME = 'schemes';

    protected $mapping = [
        'grade' => 'grade',
        'subject' => 'subject',
        'topic' => 'topic',
        'duration' => 'duration',
        'school' => 'school',
        'objective' => 'objective',
        'methodology' => 'methodology',
        'reference' => 'reference',
        'assessment' => 'assessment',
        'remark' => 'remark',
        'datecreated' => 'datecreated',
        'academicYear' => 'academicYear'
    ];

    public function getDatecreated()
    {
        return $this->datecreated;
    }

    public function setDatecreated($date)
    {
        $this->datecreated =  $date;
    }

    public function getAcademicYear()
    {
        return $this->academicYear;
    }

    public function setAcademicYear($year)
    {
        $this->academicYear = $year;
    }

    /**
     * @return string
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @return string
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @return number
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * @return multitype:
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return multitype:
     */
    public function getObjective()
    {
        return $this->objective;
    }

    /**
     * @return multitype:
     */
    public function getMethodology()
    {
        return $this->methodology;
    }

    /**
     * @return multitype:
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return multitype:
     */
    public function getAssessment()
    {
        return $this->assessment;
    }

    /**
     * @return multitype:
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * @param string $grade
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param string $topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
    }

    /**
     * @param string $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @param number $school
     */
    public function setSchool($school)
    {
        $this->school = $school;
    }

    /**
     * @param multitype: $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @param multitype: $objective
     */
    public function setObjective($objective)
    {
        $this->objective = $objective;
    }

    /**
     * @param multitype: $methodology
     */
    public function setMethodology($methodology)
    {
        $this->methodology = $methodology;
    }

    /**
     * @param multitype: $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * @param multitype: $assessment
     */
    public function setAssessment($assessment)
    {
        $this->assessment = $assessment;
    }

    /**
     * @param multitype: $remark
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;
    }
}
