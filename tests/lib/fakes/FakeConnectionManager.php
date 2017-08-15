<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/15/2017
 * Time: 5:34 AM
 */

namespace TwoQuakers\testing\fakes;


use Tops\db\TConnectionManager;

class FakeConnectionManager extends TConnectionManager
{
    private $config;

    public static function Create($config) {
        $result = new FakeConnectionManager();
        $result->config = $config;
        return $result;
    }
    public function getNativeConfiguration()
    {
        if (isset($this->config)) {
            return $this->config;
        }
        $result = new \stdClass();
        $result->default = 'fakedb';
        $result->connections =
            array(
                'fakedb' => array(
                    'database' => 'dbname',
                    'user' => 'username',
                    'pwd' => 'password',
                )
            );

        return $result;
    }
}