<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 7/3/2017
 * Time: 12:51 PM
 */

namespace Tops\db;

use \PDO;
use Tops\sys\TPath;

class TDatabase
{
    private static $dbconfig = array();
    private static $defaultDbName;
    private static $errorMode;

    private static function getDbConfiguration()
    {
        if (empty(self::$dbconfig)) {
            $configPath = TPath::getConfigPath() . 'database.ini';
            $ini = parse_ini_file($configPath, true);
            if ($ini === false) {
                throw new \Exception("No database configuration file: '$configPath'");
            }
            $keys = array_keys($ini);
            foreach ($keys as $key) {
                $settings = $ini[$key];
                if ($key == 'settings') {
                    self::$defaultDbName = empty($settings['default']) ? 'database' : $settings['default'];
                    self::$errorMode = isset($settings['errormode']) ? $settings['errormode'] : PDO::ERRMODE_EXCEPTION;
                } else {
                    if (empty($settings['database']) ||
                        empty($settings['user']) ||
                        empty($settings['pwd'])
                    ) {
                        throw new \Exception("Incomplete database configuration in database.ini, section:$key");
                    }
                    $server = empty($settings['server']) ? 'localhost' : $settings['server'];
                    $dbname = $settings['database'];
                    $params = new \stdClass();
                    $params->user = $settings['user'];
                    $params->pwd = $settings["pwd"];
                    $params->dsn = "mysql:host=$server;dbname=$dbname";
                    self::$dbconfig[$key] = $params;
                }
            }
        }
        return self::$dbconfig;
    }

    public static function getConnectionParams($key = null)
    {
        $connections = self::getDbConfiguration();
        if ($key == null) {
            $key = self::$defaultDbName;
        }
        if (!array_key_exists( $key, $connections)) {
            throw new \Exception("Connection parameters for database '$key' not found.");
        }
        return $connections[$key];
    }

    public static function getConnection($key = null)
    {
        $settings = self::getConnectionParams($key);
        $dbh = new PDO($settings->dsn,$settings->user,$settings->pwd);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, self::$errorMode);
        return $dbh;
    }


}