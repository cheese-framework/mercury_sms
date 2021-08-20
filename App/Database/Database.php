<?php

namespace App\Database;

use PDO, PDOException;
use Throwable;

class Database
{

    private $dbname = DBNAME;
    private $dbuser = DBUSER;
    private $dbpass = DBPASS;
    private $dbhost = DBHOST;
    public $dbh;
    private $error;
    public $stmt;
    private static ?Database $instance = null;

    private function __construct()
    {
        // Set DSN
        $dsn = 'mysql:host=' . $this->dbhost . ';dbname=' . $this->dbname;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        // Create a new PDO instanace
        try {
            $this->dbh = new PDO($dsn, $this->dbuser, $this->dbpass, $options);
        }  // Catch any errors
        catch (PDOException $e) {
            echo $e->getMessage();
        } catch (Throwable $th) {
            echo $th->getMessage();
        }
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Prepare statement with query
    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);
        return $this;
    }

    // Bind values
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
        return $this;
    }

    // Execute the prepared statement
    public function execute()
    {
        return $this->stmt->execute();
    }

    // Get result set as array of objects
    public function resultset()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get single record as object
    public function single()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // Get record row count
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    // Returns the last inserted ID
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }
}
