<?php
namespace Db;


class DbConnect
{
    private $_params;
    private $_connection;
    private static $_instance;

    private function __clone()
    {

    }

    private function __construct()
    {
        try {
            $this->_params = require 'config/db.conf.php';
            $this->_connection = new \PDO(
                "mysql:host={$this->_params['host']};dbname={$this->_params['dbname']}",
                $this->_params['username'],
                $this->_params['password']
            );
            $this->_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch(\PDOException $e) {
            die('ERROR: ' . $e->getMessage());
        }
    }

    public static function getInstance() {
        if(!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getConnection() {
        return $this->_connection;
    }


}