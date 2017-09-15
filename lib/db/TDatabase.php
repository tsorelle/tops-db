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

class TDatabase
{
    private static $dbconfig = array();
    private static $defaultDbName;
    private static $aliases = array();
    private static $errorMode = PDO::ERRMODE_EXCEPTION;


    private static function getDbConfiguration()
    {

        if (empty(self::$dbconfig)) {
            self::$defaultDbName = 'database';
            $configPath = TPath::getConfigPath() . 'database.ini';
            if (!file_exists($configPath)) {
                self::$dbconfig = array();
            }
            else {
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
                    }
                    else if ($key == 'alias') {
                        self::$aliases = $settings;
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
            if (TObjectContainer::HasDefinition('tops.connections')) {
                /**
                 * @var TConnectionManager
                 */
                $connectionManager = TObjectContainer::Get('tops.connections');
                $config = $connectionManager->getNativeConfiguration();
                if ($config) {
                    if (self::$defaultDbName == 'database' && !empty($config->default)) {
                        self::$defaultDbName = $config->default;
                    }
                    self::$dbconfig=array_merge(self::$dbconfig,$config->connections);
                }
            }
        }
        return self::$dbconfig;
    }

    public static function ClearCache() {
        self::$dbconfig = null;
        self::$defaultDbName = null;
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
        $connectionKey = self::$defaultDbName;
        if ($key != null) {
            if (array_key_exists($key,$connections)) {
                $connectionKey = $key;
            }
            else  if (array_key_exists( $key, self::$aliases)) {
                $connectionKey = self::$aliases[$key];
            }
        }
        return $connections[$connectionKey];
    }

    public static function getPersistentConnection($key = null) {
        $connection = self::getConnection($key,array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            pdo::ATTR_PERSISTENT => true));
        return $connection;
    }

    public static function getConnection($key = null,array $options=array())
    {
        $settings = self::getConnectionParams($key);
        if (!array_key_exists(PDO::ATTR_ERRMODE,$options)) {
            $options[PDO::ATTR_ERRMODE] = self::$errorMode;
        }
        $dbh = new PDO($settings->dsn,$settings->user,$settings->pwd,$options);
        // $dbh->setAttribute(PDO::ATTR_ERRMODE, self::$errorMode);
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
        $sql = @file_get_contents($script);
        if (empty($sql)) {
            throw new \Exception('SQL Script not found.');
        }
        $query = $connection->prepare($sql);
        $result = $query->execute();
        return $result;
    }

    public static function tableExists($tableName, $connection=null) {
        if (empty($connection)) {
            $connection = null;
        }
        if (gettype($connection) !== 'object') {
            $connection = self::getConnection($connection);
        }
        $sql = 'SELECT COUNT(*) as tableCount FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?';
        $stmt = $connection->prepare($sql);
        $stmt->execute([$tableName]);
        $result = $stmt->fetch();
        return $result['tableCount'] != 0;
    }

    public static function rowCount($tableName, $connection=null)
    {
        if (empty($connection)) {
            $connection = null;
        }
        if (gettype($connection) !== 'object') {
            $connection = self::getConnection($connection);
        }

        if (self::tableExists($tableName,$connection)) {
            $sql = 'SELECT COUNT(*) as rowCount FROM '.$tableName;
            $stmt = $connection->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            return  (int)$result['rowCount'];
        }
        return false;

    }



}