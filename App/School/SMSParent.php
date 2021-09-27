<?php

namespace App\School;

use App\Core\Helper;
use App\Notifiable\Notifiable;
use App\Database\Database;
use App\Database\Finder;
use App\Database\Paginate;
use App\Entity\Parents;
use App\Entity\ParentsService;

class SMSParent extends Notifiable
{

    public $name;
    public $email;
    public $id;
    private $school;
    private $phone;
    private $db;
    private ParentsService $service;
    private Parents $parent;

    public function __construct($name, $email, $school, $phone, $id = 0)
    {
        $this->name = $name;
        $this->email = $email;
        $this->school = $school;
        $this->id = $id;
        $this->phone = $phone;
        $this->db = Database::getInstance();
    }

    public function addParent()
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'school' => $this->school,
            'password' => password_hash(1234, PASSWORD_DEFAULT)
        ];
        $this->service = new ParentsService(Database::getInstance());
        $this->parent = Parents::arrayToEntity($data, new Parents());
        if (!$this->emailExists($data['email'])) {
            $this->service->save($this->parent);
        } else {
            throw new \Exception("E-mail already exists");
        }
    }

    public function getMyStudents($year, $type = "all")
    {
        if ($type == "all") {
            $this->db->query("SELECT * FROM students WHERE paremail=? AND academicYear=?");
            $this->db->bind(1, $this->email);
            $this->db->bind(2, $year);
        } else {
            $this->db->query("SELECT * FROM students WHERE paremail=? AND school=? AND academicYear=?");
            $this->db->bind(1, $this->email);
            $this->db->bind(2, $this->school);
            $this->db->bind(3, $year);
        }
        $result = $this->db->resultset();
        if ($this->db->rowCount() > 0) {
            return $result;
        }
        $this->hasStudent = false;
        return null;
    }

    public function getLinked()
    {
        $this->db->query("SELECT paremail FROM students WHERE paremail=? AND school=?");
        $this->db->bind(1, $this->email);
        $this->db->bind(2, $this->school);
        $this->db->execute();
        return $this->db->rowCount();
    }

    private function emailExists($email): bool
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS parents (
                id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
                name VARCHAR(200) NOT NULL,
                email VARCHAR(200) NOT NULL,
                school INT NOT NULL
            );
        ")->execute();

        $this->db->getInstance();
        $this->db->query("SELECT email FROM parents WHERE email=? AND school=?")
            ->bind(1, $email)
            ->bind(2, $this->school)
            ->execute();
        return $this->db->rowCount() > 0;
    }

    public static function getParents($school, $limit, $lines)
    {
        $parentsLists = NULL;
        $sql = Finder::select("parents")->where('school=:school')->order("name", "ASC");
        $paginate = new Paginate($sql::getSql(), $limit, $lines);
        foreach ($paginate->paginate(Database::getInstance(), [':school' => $school]) as $row) {
            $temp = new SMSParent($row->name, $row->email, $row->school, $row->phone, $row->id);
            $parentsLists[] = $temp;
        }
        return $parentsLists;
    }

    public static function removeParent($id, $school)
    {
        $service = new ParentsService(Database::getInstance());
        $parent = new Parents();
        $parent->setId($id);
        $parent->setSchool($school);
        $service->remove($parent);
    }

    public static function getParentEmail($studentId)
    {
        $db = Database::getInstance();
        $db->query("SELECT paremail FROM students WHERE studentId = ?");
        $db->bind(1, $studentId);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->paremail;
        } else {
            return "";
        }
    }

    public static function getParentIdByEmail($email)
    {
        $db = Database::getInstance();
        $db->query("SELECT id FROM parents WHERE email=?");
        $db->bind(1, $email);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            return $data->id;
        }
        return 0;
    }

    // authentication
    public static function login($email, $password)
    {
        $service = new ParentsService(Database::getInstance());
        $data =  $service->fetchByEmail($email);
        if ($data) {
            if (password_verify($password, $data->password)) {
                return $data;
            }
            return false;
        }
        return false;
    }
}
