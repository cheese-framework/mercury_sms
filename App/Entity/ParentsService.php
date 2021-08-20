<?php

namespace App\Entity;

use App\Database\Database;
use App\Database\Finder;
use PDO, PDOException, Throwable;

class ParentsService
{

    protected Database $connection;

    public function __construct(Database $connection)
    {
        $this->connection = $connection;
    }

    public function fetchById($id)
    {
        $stmt = $this->connection->dbh->prepare(
            Finder::select('parents')->where('id = :id')::getSql()
        );
        $stmt->execute([':id' => (int) $id]);
        return Parents::arrayToEntity($stmt->fetch(PDO::FETCH_ASSOC), new Parents());
    }

    public function fetchByEmail($email)
    {
        $stmt = $this->connection->dbh->prepare(
            Finder::select('parents')->where('email = :email')::getSql()
        );
        $stmt->execute([':email' => (string) $email]);
        return Parents::arrayToEntity($stmt->fetch(PDO::FETCH_ASSOC), new Parents());
    }

    public function fetchBySchool($schoolId)
    {
        $stmt = $this->connection->dbh->prepare(
            Finder::select('parents')
                ->where('school = :school')::getSql()
        );
        $stmt->execute([':school' => (int) $schoolId]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            yield Parents::arrayToEntity($row, new Parents());
        }
    }

    public function save(Parents $parent)
    {
        if ($parent->getId() && $this->fetchById($parent->getId())) {
            return $this->doUpdate($parent);
        } else {
            return $this->doInsert($parent);
        }
    }

    protected function doUpdate($cust)
    {
        // get properties in the form of an array
        $values = $cust->entityToArray();
        // build the SQL statement
        $update = 'UPDATE ' . $cust::TABLE_NAME;
        $where = ' WHERE id = ' . $cust->getId();
        // unset ID as we want do not want this to be updated
        unset($values['id']);
        return $this->flush($update, $values, $where);
    }

    protected function doInsert($cust)
    {
        $values = $cust->entityToArray();
        $email = $cust->getEmail();
        unset($values['id']);
        $insert = 'INSERT INTO ' . $cust::TABLE_NAME . ' ';
        if ($this->flush($insert, $values)) {
            return $this->fetchByEmail($email);
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

    public function remove(Parents $parent)
    {
        $sql = 'DELETE FROM ' . $parent::TABLE_NAME . ' WHERE id = :id AND school = :school';
        $stmt = $this->connection->dbh->prepare($sql);
        $stmt->execute([':id' => $parent->getId(), ':school' => $parent->getSchool()]);
        return ($this->fetchById($parent->getId())) ? FALSE : TRUE;
    }
}
