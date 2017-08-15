<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/15/2017
 * Time: 4:50 AM
 */

namespace Tops\db;


abstract class TConnectionManager
{
    public function makeParameterObject(
        $database,
        $user,
        $pwd,
        $server = 'localhost')
    {
        $result = new \stdClass();
        $result->database = $database;
        $result->user = $user;
        $result->pwd = $pwd;
        if ($server != 'localhost' && !empty($server)) {
            $result->server = $server;
        }
        return $result;
    }


    abstract public function getNativeConfiguration();
}