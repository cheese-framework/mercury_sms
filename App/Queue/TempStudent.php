<?php

namespace App\Queue;

use App\Notifiable\Notifiable;
use App\Database\Database;

class TempStudent extends Notifiable
{

    protected function getNotificationLinkTwilio()
    {
        $db = Database::getInstance();
        $db->query("SELECT phone FROM students WHERE studentId=?");
        $db->bind(1, $this->id);
        $record = $db->single();
        if ($db->rowCount() > 0) {
            return ($record->phone != "") ? $record->phone : "";
        } else {
            return "";
        }
    }

    protected function getNotificationLinkMail()
    {
        $db = Database::getInstance();
        $db->query("SELECT fullname,email FROM students WHERE studentId=?");
        $db->bind(1, $this->id);
        $record = $db->single();
        if ($db->rowCount() > 0) {
            if ($record->email != "" && $record->fullname != "") {
                return [$record->email => $record->fullname];
            }
            return NULL;
        } else {
            return NULL;
        }
    }
}
