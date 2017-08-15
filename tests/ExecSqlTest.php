<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/12/2017
 * Time: 7:43 AM
 */

use PHPUnit\Framework\TestCase;
use Tops\sys\TPath;

class ExecSqlTest extends TestCase
{
    private function ClearCaches() {
        \Tops\sys\TObjectContainer::ClearCache();
        \Tops\db\TDatabase::ClearCache();
    }

    public function testExecSql() {
        $this->ClearCaches();
        $token=\Tops\sys\TSession::GetSecurityToken();
        $script = TPath::GetFileRoot()."tests/sql/test-exec-sql.sql";
        $this->assertTrue(file_exists($script),'Cannot find test script.');
        $expected = 1;

        $actual = \Tops\db\TDatabase::ExecuteSql($token,$script);
        $this->assertTrue($actual,'Wrong return value from ExecuteSql()');

        $script = TPath::GetFileRoot()."tests/sql/cleanup.sql";
        $actual = \Tops\db\TDatabase::ExecuteSql($token, $script);
        $this->assertTrue($actual,'Cleanup failed in ExecuteSql()');
    }

}
