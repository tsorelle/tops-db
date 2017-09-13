<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/13/2017
 * Time: 10:07 AM
 */

namespace Tops\db;


use Tops\sys\TConfiguration;
use Tops\sys\TPath;

class TDbInstaller
{
    private $log = array();

    private $topsTables = array(
        'tops_rolepermissions',
        'tops_permissions',
        'tops_mailboxes'
    );

    public function installTopsSchema($scriptPath='application/install/sql', $databaseName=null) {
        $scriptLocation = TPath::fromFileRoot($scriptPath);
        $connection = TDatabase::getConnection($databaseName);
        foreach ($this->topsTables as $tableName) {
            $result = $this->createTable($tableName,$scriptPath,$connection);
        }
        if (empty($this->log)) {
            $this->log[] = 'Tops schema: no changes needed.';
        }
        return $this->log;
    }

    protected function createTable($tableName,$scriptPath,$connection) {
        $result = TDatabase::rowCount($tableName);
        if ($result === false) {
            $token=\Tops\sys\TSession::GetSecurityToken();
            $script = TPath::fromFileRoot($scriptPath).'/'."create-table-$tableName.sql";
            TDatabase::ExecuteSql($token,$script,$connection);
            $this->writeLog("Created table: $tableName");
            return 0;
        }
        return $result;
    }

    protected function clearLog() {
        $this->log = array();
    }

    protected function writeLog($message) {
        if (!empty($message)) {
            $this->log[] = $message;
        }
    }
}