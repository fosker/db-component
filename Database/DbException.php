<?php
/**
 * Created by PhpStorm.
 * User: Богдан
 * Date: 06.02.2016
 * Time: 23:49
 */

namespace Db;


class DbException extends \Exception
{
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        $message = __CLASS__ . ' ' . $message;
        parent::__construct($message, $code, $previous);
    }
}