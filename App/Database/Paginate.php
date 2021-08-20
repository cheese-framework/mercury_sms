<?php

namespace App\Database;

use PDOException, Throwable;

class Paginate
{
    const DEFAULT_LIMIT = 10;
    const DEFAULT_OFFSET = 0;
    protected $sql;
    protected $page;
    protected $linesPerPage;

    public function __construct($sql, $page, $linesPerPage)
    {
        $offset = $page * $linesPerPage;
        if ($sql instanceof Finder) {
            $sql->limit($linesPerPage);
            $sql->offset($offset);
            $this->sql = $sql::getSql();
        } elseif (is_string($sql)) {
            switch (TRUE) {
                case (stripos($sql, 'LIMIT') && strpos($sql, 'OFFSET')):
                    // no action needed
                    break;
                case (stripos($sql, 'LIMIT')):
                    $sql .= ' LIMIT ' . self::DEFAULT_LIMIT;
                    break;
                case (stripos($sql, 'OFFSET')):
                    $sql .= ' OFFSET ' . self::DEFAULT_OFFSET;
                    break;
                default:
                    $sql .= ' LIMIT ' . self::DEFAULT_LIMIT;
                    $sql .= ' OFFSET ' . self::DEFAULT_OFFSET;
                    break;
            }
            $this->sql = preg_replace(
                '/LIMIT \d+.*OFFSET \d+/Ui',
                'LIMIT ' . $linesPerPage . ' OFFSET ' . $offset,
                $sql
            );
        }
    }

    public function paginate(
        Database $connection,
        $params = array()
    ) {
        try {
            $connection->query($this->getSql());
            if ($params) {
                foreach ($params as $key => $value) {
                    $connection->bind($key, $value);
                }
            }
            $result = $connection->resultset();
            foreach ($result as $data) {
                yield $data;
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return FALSE;
        } catch (Throwable $e) {
            error_log($e->getMessage());
            return FALSE;
        }
    }

    public function getSql()
    {
        return $this->sql;
    }
}
