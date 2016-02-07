<?php

namespace Db\Adapters;

use Db\DbConnect;
use Db\DbException;

abstract class MySQLAdapter
{
    static protected $conn;
    static protected $table_name;
    protected $params = [];

    public function __set($prop, $value)
    {
        $this->params[$prop] = $value;
    }

    public function __get($prop)
    {
        try {
            if (array_key_exists($prop, $this->params)) {
                return $this->params[$prop];
            } else {
                throw new DbException("Property $prop not exist");
            }
        } catch (DbException $e) {
            die('ERROR: ' . $e->getMessage());
        }
    }

    public static function find(array $whereSth)
    {
        self::$conn = DbConnect::getInstance()->getConnection();
        $table = static::$table_name;

        $colName = array_keys($whereSth);
        $colValue = array_values($whereSth);

        $where = "";
        for($i = 0; $i < count($whereSth); $i++) {
            $where .= $colName[$i] . "=:v$i AND ";
        }

        $where = rtrim($where, ' AND ');

        $query = 'SELECT * FROM ' .$table. ' WHERE ' .$where;
        $sth = self::$conn->prepare($query);

        for ($i = 0; $i < count($whereSth); $i++) {
            $sth->bindValue(':v' . $i, $colValue[$i]);
        }

        $sth->execute();

        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);

        $objects = [];
        foreach ($res as $val) {
            $class = static::class;
            $objects[] = new $class();
        }
        for ($i = 0; $i <= count($res) - 1; $i++) {
            foreach ($res[$i] as $k => $v) {
                $objects[$i]->$k = $v;
            }
        }
        return $objects;
    }

    public function delete()
    {
        self::$conn = DbConnect::getInstance()->getConnection();
        $table = static::$table_name;

        $query = "DELETE FROM $table WHERE id = " . $this->params['id'];
        $sth = self::$conn->prepare($query);

        $sth->execute();
    }

    public function save()
    {
        self::$conn = DbConnect::getInstance()->getConnection();
        $table = static::$table_name;

        $colName = array_keys($this->params);
        $colValue = array_values($this->params);

        $fields = '';
        $values = '';

        if (array_key_exists('id', $this->params)) {
            for ($i = 0; $i < count($this->params); $i++) {
                $fields .= "$colName[$i]=:v$i, ";
            }
            $fields = rtrim($fields, ", ");

            $query = 'UPDATE ' . $table . " SET " . $fields . " WHERE id=" . $this->params['id'];
            $sth = self::$conn->prepare($query);

            for ($i = 0; $i < count($this->params); $i++) {
                $sth->bindValue(':v' . $i, $colValue[$i]);
            }
            $sth->execute();

        } else {
            for ($i = 0; $i < count($this->params); $i++) {
                $fields .= "$colName[$i], ";
                $values .= ":v" . $i . ", ";
            }

            $fields = rtrim($fields, ", ");
            $values = rtrim($values, ", ");

            $query = "INSERT INTO " . $table . " (" . $fields . ") VALUES (" . $values . ")";
            $sth = self::$conn->prepare($query);
            for ($i = 0; $i < count($this->params); $i++) {
                $sth->bindValue(':v' . $i, $colValue[$i]);
            }
            $sth->execute();
        }
    }
}