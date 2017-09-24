<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/13/2017
 * Time: 10:07 AM
 */

namespace Tops\db;


use Tops\sys\TIniSettings;
use Tops\sys\TPath;

class TDbInstaller
{
    private $log = array();

    public function testInstallSchema(TIniSettings $config, $scriptPath = 'application/install') {
        $tables = $config->getSection('tables');
        foreach ($tables as $tableName => $expectedCount) {
            $script = TPath::fromFileRoot($scriptPath) . '/' . "create-table-$tableName.sql";
            $message = file_exists($script) ? "TEST: Created table: $tableName" : "Create script for $tableName not found.";
            $this->writeLog($message);
        }
        if (empty($this->log)) {
            $this->log[] = 'Schema: no changes needed.';
        }
        return $this->log;
    }

    public function installSchema(TIniSettings $config, $scriptPath = 'application/install')
    {
        $databaseName = $config->getValue('database', 'settings', null);
        $connection = TDatabase::getConnection($databaseName);
        $tables = $config->getSection('tables');
        foreach ($tables as $tableName => $expectedCount) {
            $this->createTable($tableName, $expectedCount, $scriptPath, $connection);
        }
        if (empty($this->log)) {
            $this->log[] = 'Schema: no changes needed.';
        }
        return $this->log;
    }

    public function dropSchema(TIniSettings $config, $testing=false)
    {
        $databaseName = $config->getValue('database', 'settings', null);
        $connection = TDatabase::getConnection($databaseName);
        $tables = array_reverse($config->getSection('tables'));
        foreach ($tables as $tableName => $expectedCount) {
            if ($testing) {
                $this->log[] = "TEST: Would have dropped table: $tableName";
            }
            else {
                $query = $connection->prepare("DROP TABLE $tableName");
                $query->execute();
                $this->log[] = "Dropped table: $tableName";
            }
        }
        if (empty($this->log)) {
            $this->log[] = 'Schema: no changes needed.';
        }
        return $this->log;
    }

    protected function createTable($tableName, $expectedRowCount, $scriptPath, $connection)
    {
        $result = TDatabase::rowCount($tableName);
        if ($result === false || $result < $expectedRowCount) {
            $token = \Tops\sys\TSession::GetSecurityToken();
            $script = TPath::fromFileRoot($scriptPath) . '/' . "create-table-$tableName.sql";
            TDatabase::ExecuteSql($token, $script, $connection);
            $this->writeLog("Created table: $tableName");
            return 0;
        }
        return $result;
    }

    protected function clearLog()
    {
        $this->log = array();
    }

    protected function writeLog($message)
    {
        if (!empty($message)) {
            $this->log[] = $message;
        }
    }
}