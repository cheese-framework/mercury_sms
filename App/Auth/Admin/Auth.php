<?php

namespace App\Auth\Admin;

use App\Database\Database;
use Exception;

class Auth
{

    public static function login($email, $password)
    {
        $db = Database::getInstance();
        $user = $db->query("SELECT * FROM admin WHERE email=? || username=?")
            ->bind(1, $email)
            ->bind(2, $email)
            ->single();
        if ($db->rowCount() > 0) {
            $passwordFromQuery = $user->password;
            // verify password
            if (password_verify($password, $passwordFromQuery)) {
                return $user;
            } else {
                throw new Exception("Invalid credentials. Check your entries.");
            }
        } else {
            throw new Exception("User not found");
        }
    }

    public static function createAdmin($username, $email, $password, $role)
    {
        $db = Database::getInstance();
        $db->query("INSERT INTO admin (username, email, password, role) VALUES(?,?,?,?)");
        $db->bind(1, $username);
        $db->bind(2, $email);
        $db->bind(3, $password);
        $db->bind(4, $role);
        return $db->execute();
    }
}
