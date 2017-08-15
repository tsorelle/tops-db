<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 7/3/2017
 * Time: 12:51 PM
 */

namespace Tops\db;

use \PDO;
use PHPUnit\Runner\Exception;
use Tops\sys\TObjectContainer;
use Tops\sys\TPath;
use Tops\sys\TSession;
use Tops\db\TConnectionManager;

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
            if (TObjectContainer::HasDefinition('tops.connections')) {
                /**
                 * @var TConnectionManager
                 */
                $connectionManager = TObjectContainer::Get('tops.connections');
                $config = $connectionManager->getNativeConfiguration();
                if ($config) {
                    if (!empty($config->default)) {
                        self::$defaultDbName = $config->default;
                    }
                    self::$dbconfig=array_merge($ini,$config->connections);
                }
            }
        }
        return self::$dbconfig;
    }

    public static function getDbConfigurationForTest() {
        self::$dbconfig = null;
        $result = new \stdClass();
        $result->connections = self::getDbConfiguration();
        $result->default = self::$defaultDbName;
        return $result;
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

    public static function ExecuteSql($token, $script, $connection=null) {
        if ($token != TSession::GetSecurityToken()) {
            throw new \Exception('Unauthorized database access');
        }
        if (empty($connection)) {
            $connection = null;
        }
        if (gettype($connection) !== 'object') {
            $connection = self::getConnection($connection);
        }
        $sql = file_get_contents($script);
        if (empty($sql)) {
            throw new Exception('SQL Script not found.');
        }
        $query = $connection->prepare($sql);
        $result = $query->execute();
        return $result;
    }


}