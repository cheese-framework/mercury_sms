<?php

use App\Database\Database;

class Student
{
    protected $id;

    public static function login($userId, $password)
    {
        $db = Database::getInstance();
        $db->query("SELECT studentId,fullname,class,academicYear,admissionno,school,email,password FROM students WHERE email=? OR admissionno=?");
        $db->bind(1, $userId);
        $db->bind(2, $userId);
        $data = $db->single();
        if ($db->rowCount() > 0) {
            $passwordDB = $data->password;
            if (password_verify($password, $passwordDB)) {
                return $data;
            }
            throw new Exception("Password is incorrect || Invalid credentials");
        }
        throw new Exception("User not found");
    }
}
