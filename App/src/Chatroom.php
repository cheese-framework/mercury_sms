<?php

class Chatroom
{
    private $chatId;
    private $userId;
    private $message;
    private $createdOn;
    private $school;
    private $connection;

    public function setChatId($id)
    {
        $this->chatId = $id;
    }

    public function getChatId()
    {
        return $this->chatId;
    }

    public function setSchoolId($id)
    {
        $this->school = $id;
    }

    public function getSchoolId()
    {
        return $this->school;
    }

    public function setUserId($id)
    {
        $this->userId = $id;
    }

    public function getUserId()
    {
        return $this->userId;
    }


    public function setMessage($msg)
    {
        $this->message = $msg;
    }

    public function getMessage()
    {
        return $this->message;
    }


    public function setCreatedOn($date)
    {
        $this->createdOn = $date;
    }

    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    public function __construct()
    {
        require_once '../classes/Database.php';
        $db = Database::getInstance();
        $this->connection = $db;
    }

    public function saveChat()
    {
        $this->connection->query("INSERT INTO chatroom (userId, school, msg, created_on)
            VALUES(?,?,?,?)");
        $this->connection->bind(1, $this->getUserId());
        $this->connection->bind(2, $this->getSchoolId());
        $this->connection->bind(3, $this->getMessage());
        $this->connection->bind(4, $this->getCreatedOn());
        $this->connection->execute();
    }
}
