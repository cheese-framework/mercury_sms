<?php

namespace App\Database;

use App\Iterator\LargeFile;
use Exception;

class CSVUploader
{

    private $csvFile;
    private $tableName;
    private $fields = [];
    private $iterator;

    public function __construct($file, $tableName, $fields)
    {
        $this->csvFile = $file;
        $this->tableName = $tableName;
        $this->fields = $fields;
        $this->iterator = (new LargeFile($this->csvFile))->getIterator('Csv');
    }

    public function uploadToDB()
    {
        $dbh = Database::getInstance()->dbh;
        $fields = "";
        $values = [];

        if ($this->fields) {
            $fields = implode(',', $this->fields);
            foreach ($this->fields as $f) {
                $values[] = "?";
            }
            $values = implode(",", $values);
            $sql = "INSERT INTO {$this->tableName} ($fields) VALUES($values)";
            $statement = $dbh->prepare($sql);
            try {
                $index = 0;
                foreach ($this->iterator as $row) {
                    if ($row[15] != "") {
                        $row[15] = password_hash($row[15], PASSWORD_DEFAULT);
                    }
                    if ($index > 0) {
                        $statement->execute($row);
                    }
                    $index++;
                }
            } catch (\Throwable $th) {
                // throw new Exception($th->getMessage());
            }
        }
        return;
    }
}
