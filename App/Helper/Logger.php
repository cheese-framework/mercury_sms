<?php

namespace App\Helper;

use App\Database\Database;

class Logger
{
    public static string $log;
    public static string $time;
    public static string $event;
    public static int $level;
    private static int $school;

    public function __construct($log, $event, $school, $level = 1, $time = "")
    {
        self::$log = $log;
        self::$event = $event;
        self::$level = $level;
        self::$school = $school;
        self::$time = ($time == "") ? date("d-m-Y h:i:s") : $time;
    }

    public static function save()
    {
        $db = Database::getInstance();
        $db->query("
            CREATE TABLE IF NOT EXISTS logs (
                id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
                log TEXT NOT NULL,
                event VARCHAR(150) NOT NULL,
                level INT NOT NULL,
                school INT NOT NULL,
                time VARCHAR(100) NOT NULL
            );
        ");
        $db->execute();
        $db->query("INSERT INTO logs (log,event,school,level,time) VALUES(?,?,?,?,?)");
        $db->bind(1, self::$log);
        $db->bind(2, self::$event);
        $db->bind(3, self::$school);
        $db->bind(4, self::$level);
        $db->bind(5, self::$time);
        $db->execute();
    }

    public static function logs($school)
    {
        $db = Database::getInstance();
        $db->query("SELECT * FROM logs WHERE school=? ORDER BY id DESC LIMIT 20")->bind(1, $school);
        $data = $db->resultset();
        if ($db->rowCount() > 0) {
            $logs = [];
            foreach ($data as $datum) {
                $logs[] = $datum;
            }
            return $logs;
        }
        return null;
    }

    public static function delete($id, $school)
    {
        $db = Database::getInstance();
        if ($id == "all") {
            return $db->query("DELETE FROM logs WHERE school=?")
                ->bind(1, $school)
                ->execute();
        } else {
            return $db->query("DELETE FROM logs WHERE id=? AND school=?")
                ->bind(1, $id)
                ->bind(2, $school)
                ->execute();
        }
    }
}
