<?php

namespace App\Entity;

use App\Database\Database;
use App\Database\Finder;
use App\Extra\SchemeOfWork;
use PDO, Throwable, PDOException;

class SchemeOfWorkService
{
    protected Database $connection;

    public function __construct(Database $connection)
    {
        $this->connection = $connection;
    }

    public function fetchById($id, $school)
    {
        $stmt = $this->connection->dbh->prepare(
            Finder::select('schemes')->where('id = :id')->and('school = :school')::getSql()
        );
        $stmt->execute([':id' => (int) $id, ':school' => $school]);
        return SchemeOfWork::arrayToEntity($stmt->fetch(PDO::FETCH_ASSOC), new SchemeOfWork());
    }

    public function save(SchemeOfWork $schemeOfWork)
    {
        if ($schemeOfWork->getId() && $this->fetchById($schemeOfWork->getId(), $schemeOfWork->getSchool())) {
            return $this->doUpdate($schemeOfWork);
        } else {
            return $this->doInsert($schemeOfWork);
        }
    }


    protected function doUpdate($scheme)
    {
        // get properties in the form of an array
        $values = $scheme->entityToArray();
        // build the SQL statement
        $update = 'UPDATE ' . $scheme::TABLE_NAME;
        $where = ' WHERE id = ' . $scheme->getId();
        // unset ID as we want do not want this to be updated
        unset($values['id']);
        return $this->flush($update, $values, $where);
    }

    protected function doInsert($scheme)
    {
        $values = $scheme->entityToArray();
        $id = $scheme->getId();
        unset($values['id']);
        $insert = 'INSERT INTO ' . $scheme::TABLE_NAME . ' ';
        if ($this->flush($insert, $values)) {
            return $this->fetchById($id, $scheme->getSchool());
        } else {
            return FALSE;
        }
    }


    protected function flush($sql, $values, $where = '')
    {
        $sql .= ' SET ';
        foreach ($values as $column => $value) {
            $sql .= $column . ' = :' . $column . ',';
        }
        // get rid of trailing ','
        $sql
            = substr($sql, 0, -1) . $where;
        $success = FALSE;
        try {
            $stmt = $this->connection->dbh->prepare($sql);
            $stmt->execute($values);
            $success = TRUE;
        } catch (PDOException $e) {
            error_log(__METHOD__ . ':' . __LINE__ . ':'
                . $e->getMessage());
            $success = FALSE;
        } catch (Throwable $e) {
            error_log(__METHOD__ . ':' . __LINE__ . ':'
                . $e->getMessage());
            $success = FALSE;
        }
        return $success;
    }

    public function remove(SchemeOfWork $schemeOfWork)
    {
        $sql = 'DELETE FROM ' . $schemeOfWork::TABLE_NAME . ' WHERE id = :id AND school = :school';
        $stmt = $this->connection->dbh->prepare($sql);
        $stmt->execute([':id' => $schemeOfWork->getId(), ':school' => $schemeOfWork->getSchool()]);
        return ($this->fetchById($schemeOfWork->getId(), $schemeOfWork->getSchool())) ? FALSE : TRUE;
    }
}
