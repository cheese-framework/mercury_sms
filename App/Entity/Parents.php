<?php

namespace App\Entity;

class Parents extends Base
{
    const TABLE_NAME = 'parents';
    public $id = 0;
    public $name = '';
    public $email = '';
    public $phone = '';
    public $school = 0;
    protected $mapping = [
        'id' => 'id',
        'name' => 'name',
        'email' => 'email',
        'phone' => 'phone',
        'school' => 'school',
        'password' => 'password'
    ];

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = (string) $name;
    }


    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = (string) $email;
    }


    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = (string) $phone;
    }

    public function getSchool(): int
    {
        return $this->school;
    }

    public function setSchool($school)
    {
        $this->school = (int) $school;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = (string) $password;
    }
}
