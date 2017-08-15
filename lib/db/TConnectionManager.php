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
    public function makeDbParameters(
        $database,
        $user,
        $pwd,
        $server = 'localhost')
    {
        $result = array(
            'database' => $database,
            'user' => $user,
            'pwd' => $pwd
        );
        if ($server != 'localhost' && !empty($server)) {
            $result['server'] = $server;
        }
        return $result;
    }


    abstract public function getNativeConfiguration();
}