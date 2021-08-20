<?php

namespace App\Auth;

use App\Database\Database;

class Populator
{

    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function createSchool($name, $email, $date, $type = NURSERY_PRIMARY)
    {
        $this->db->query("INSERT INTO school(schoolName,schoolEmail,created_date, schoolType) VALUES(?,?,?,?)");
        $this->db->bind(1, $name);
        $this->db->bind(2, $email);
        $this->db->bind(3, $date);
        $this->db->bind(4, $type);
        $this->db->execute();
        if ($this->db->rowCount() > 0) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function deleteSchool($id)
    {
        $this->db->query("DELETE FROM school WHERE schoolId=?");
        $this->db->bind(1, $id);
        return $this->db->execute();
    }
}
