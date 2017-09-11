<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/11/2017
 * Time: 6:48 AM
 */

namespace TwoQuakers\testing;

use PHPUnit\Framework\TestCase;
use Tops\sys\TPath;

class RepositoryTestFixture extends TestCase
{
    protected function ClearCaches() {
        \Tops\sys\TObjectContainer::ClearCache();
        \Tops\db\TDatabase::ClearCache();
    }

    protected function runSqlScript($script) {
        $this->ClearCaches();
        $token=\Tops\sys\TSession::GetSecurityToken();
        $script = TPath::GetFileRoot()."tests/sql/$script.sql";
        $this->assertTrue(file_exists($script),"Cannot complete test. Sql script '$script' not found.'");
        $result = \Tops\db\TDatabase::ExecuteSql($token,$script);
        return $result;
    }



}