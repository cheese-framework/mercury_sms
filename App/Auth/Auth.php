<?php

namespace App\Auth;

use App\Database\Database;
use App\Notifiable\Notifiable;

class Auth extends Notifiable
{

    private $db;
    public $email;

    protected function getNotificationLinkTwilio()
    {
        $db = Database::getInstance();
        $db->query("SELECT contact_address FROM staffs WHERE staff_email=?");
        $db->bind(1, $this->email);
        $record = $db->single();
        if ($db->rowCount() > 0) {
            return $record->contact_address;
        } else {
            throw new \Exception("Could not retrieved phone record");
        }
    }

    protected function getNotificationLinkMail()
    {
        $db = Database::getInstance();
        $db->query("SELECT staff_name,staff_email FROM staffs WHERE staff_email=?");
        $db->bind(1, $this->email);
        $record = $db->single();
        if ($db->rowCount() > 0) {
            return [$record->staff_email => $record->staff_name];
        } else {
            throw new \Exception("Could not retrieved email record");
        }
    }


    public static function generateToken()
    {
        $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        $shuffleString = str_shuffle($string);
        $token = uniqid($shuffleString);
        return $token;
    }
    public function getStatus($email)
    {
        $this->db = Database::getInstance();
        $this->db->query("SELECT verified FROM staffs WHERE staff_email=?");
        $this->db->bind(1, $email);
        $data = $this->db->single();
        if ($this->db->rowCount() > 0) {
            $isVerified = $data->verified;
            return ($isVerified == 1) ? true : false;
        }
        return false;
    }

    public static function delete($email)
    {
        $db = Database::getInstance();
        $db->query("DELETE FROM staffs WHERE staff_email=?");
        $db->bind(1, $email);
        return $db->execute();
    }

    public static function verifyUser($token)
    {
        $db = Database::getInstance();
        $db->query("UPDATE staffs SET verified=? WHERE passwordToken=?");
        $db->bind(1, 1);
        $db->bind(2, $token);
        $db->execute();
    }

    public static function getToken($token)
    {
        $db = Database::getInstance();
        $db->query("SELECT passwordToken FROM staffs WHERE passwordToken=?");
        $db->bind(1, $token);
        $db->execute();
        if ($db->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public static function updatePassword($password, $token, $email)
    {
        $db = Database::getInstance();
        $db->query("UPDATE staffs SET staff_password=? WHERE passwordToken=? AND staff_email=?")
            ->bind(1, $password)
            ->bind(2, $token)
            ->bind(3, $email)
            ->execute();
        if ($db->rowCount() > 0) {
            return true;
        }
        return false;
    }

    private static function incrementAttempt($email)
    {
        $db = Database::getInstance();
        if (self::userExists($email)) {
            $count = $db->query("SELECT loginAttempt FROM staffs WHERE staff_email=?")
                ->bind(1, $email)
                ->single();
            $attempt = $count->loginAttempt + 1;
            $db->query("UPDATE staffs SET loginAttempt=? WHERE staff_email=?")
                ->bind(1, $attempt)
                ->bind(2, $email)
                ->execute();
        } else {
            return false;
        }
    }


    public static function addToken($token, $email)
    {
        $db = Database::getInstance();
        return $db->query("UPDATE staffs SET passwordToken=? WHERE staff_email=?")
            ->bind(1, $token)
            ->bind(2, $email)
            ->execute();
    }

    public function createStaff($username, $email, $password, $id, $role = "Super-Admin", $gender = null, $dob = null, $prof = null, $acad = null, $year = null, $contact = null)
    {
        $this->db = Database::getInstance();
        $this->db->query("INSERT INTO staffs (staff_name, staff_email,staff_password,staff_role,gender,dob,profqual,acadqual,yearappoint,contact_address,staff_photo,school) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
        $this->db->bind(1, $username);
        $this->db->bind(2, $email);
        $this->db->bind(3, $password);
        $this->db->bind(4, $role);
        $this->db->bind(5, $gender);
        $this->db->bind(6, $dob);
        $this->db->bind(7, $prof);
        $this->db->bind(8, $acad);
        $this->db->bind(9, $year);
        $this->db->bind(10, $contact);
        $this->db->bind(11, "default.jpeg");
        $this->db->bind(12, $id);
        $this->db->execute();
        if ($this->db->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public function loginStaff($name, $password)
    {
        $this->db = Database::getInstance();
        $this->db->query("SELECT * FROM staffs WHERE staff_email=?");
        $this->db->bind(1, $name);
        $single = $this->db->single();

        if (!empty($single)) {
            $dbpass = $single->staff_password;
            if (password_verify($password, $dbpass)) {
                $data = [
                    "username" => $single->staff_name,
                    "id" => $single->staffId,
                    "role" => $single->staff_role,
                    "pic" => $single->staff_photo,
                    "school" => $single->school,
                    "email" => $single->staff_email
                ];
                self::resetLoginAttempt($name);
                if ($this->getStatus($name)) {
                    self::clearToken($name);
                }
                return $data;
            } else {
                self::incrementAttempt($name);
                /** @noinspection PhpUnhandledExceptionInspection */
                throw new \Exception("Password is incorrect");
            }
        }
        /** @noinspection PhpUnhandledExceptionInspection */
        throw new \Exception("User not found!");
    }

    private static function resetLoginAttempt($email)
    {
        $db = Database::getInstance();
        if (self::userExists($email)) {
            $db->query("UPDATE staffs SET loginAttempt=? WHERE staff_email=?")
                ->bind(1, 0)
                ->bind(2, $email)
                ->execute();
        }
    }

    private static function clearToken($email)
    {
        $db = Database::getInstance();
        if (self::userExists($email)) {
            $db->query("UPDATE staffs SET passwordToken=? WHERE staff_email=?")
                ->bind(1, NULL)
                ->bind(2, $email)
                ->execute();
        }
    }

    public static function getLoginAttempt($email)
    {
        $db = Database::getInstance();
        if (self::userExists($email)) {
            $data = $db->query("SELECT loginAttempt FROM staffs WHERE staff_email=?")
                ->bind(1, $email)
                ->single();
            return $data->loginAttempt;
        }
        return 0;
    }

    public static function getStaffs($school, $limit, $offset)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM staffs WHERE school=? ORDER BY staff_name LIMIT ? OFFSET ?");
        $db->bind(1, $school);
        $db->bind(2, $limit);
        $db->bind(3, $offset);
        $record = $db->resultset();
        if ($db->rowCount() > 0) {
            return $record;
        } else {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new \Exception("No staff found!");
        }
    }

    public static function updateProfile($username, $email, $password, $phone, $id)
    {
        $db = Database::getInstance();
        $db->query("UPDATE staffs SET staff_name=?, staff_email=?, staff_password=?, contact_address=? WHERE staffId=?");
        $db->bind(1, $username)
            ->bind(2, $email)
            ->bind(3, $password)
            ->bind(4, $phone)
            ->bind(5, $id)
            ->execute();
    }

    public static function userExists($email)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM staffs WHERE staff_email=?");
        $db->bind(1, $email);
        $db->execute();
        return $db->rowCount() > 0;
    }
}
