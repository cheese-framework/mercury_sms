<?php

namespace App\Database;

class Finder
{
    public static $sql = '';
    public static $instance = NULL;
    public static $prefix = '';
    public static $where = array();
    public static $control = ['', ''];

    public static function select($table, $cols = NULL)
    {
        self::$instance = new Finder();
        if ($cols) {
            self::$prefix = 'SELECT ' . $cols . ' FROM ' . $table;
        } else {
            self::$prefix = 'SELECT * FROM ' . $table;
        }
        return self::$instance;
    }

    public static function where($a = NULL)
    {
        self::$where[0] = ' WHERE ' . $a;
        return self::$instance;
    }

    public static function like($a, $b)
    {
        self::$where[] = trim($a . ' LIKE ' . $b);
        return self::$instance;
    }

    public static function is($a, $b)
    {
        self::$where[] = trim($a . ' = ' . $b);
        return self::$instance;
    }

    public static function and($a = NULL)
    {
        self::$where[] = trim('AND ' . $a);
        return self::$instance;
    }

    public static function or($a = NULL)
    {
        self::$where[] = trim('OR ' . $a);
        return self::$instance;
    }

    public static function in(array $a)
    {
        self::$where[] = 'IN ( ' . implode(',', $a) . ' )';
        return self::$instance;
    }

    public static function not($a = NULL)
    {
        self::$where[] = trim('NOT ' . $a);
        return self::$instance;
    }

    public static function order($by, $how)
    {
        self::$where[] = trim('ORDER BY ' . $by . ' ' . $how);
        return self::$instance;
    }

    public static function limit($limit)
    {
        self::$control[0] = 'LIMIT ' . $limit;
        return self::$instance;
    }

    public static function offset($offset)
    {
        self::$control[1] = 'OFFSET ' . $offset;
        return self::$instance;
    }

    public static function getSql()
    {
        self::$sql = self::$prefix
            . implode(' ', self::$where)
            . ' '
            . self::$control[0]
            . ' '
            . self::$control[1];
        preg_replace('/ /', ' ', self::$sql);
        return trim(self::$sql);
    }
}
