<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 7/3/2017
 * Time: 2:02 PM
 */

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private function ClearCaches() {
        \Tops\sys\TObjectContainer::ClearCache();
        \Tops\db\TDatabase::ClearCache();
    }
    
    public function testGetDefaultConnectionParams() {
        $this->ClearCaches();
        $actual = \Tops\db\TDatabase::getConnectionParams();
        $expected = new stdClass();
        $expected->user = 'twoquake_pnutman';
        $expected->pwd = 'Wh@tC@nstTh0u$ay';
        $expected->dsn = 'mysql:host=localhost;dbname=twoquake_test';
        $this->assertEquals($expected->user,$actual->user, 'User incorrect');
        $this->assertEquals($expected->pwd,$actual->pwd, 'Incorrect password');
        $this->assertEquals($expected->dsn,$actual->dsn, 'Incorrect dsn');
    }
    public function testGetConnectionParams() {
        $this->ClearCaches();
        $actual = \Tops\db\TDatabase::getConnectionParams('test');
        $expected = new stdClass();
        $expected->user = 'testuser';
        $expected->pwd = 'testpwd';
        $expected->dsn = 'mysql:host=testserver;dbname=testdb';
        $this->assertEquals($expected->user,$actual->user, 'User incorrect');
        $this->assertEquals($expected->pwd,$actual->pwd, 'Incorrect password');
        $this->assertEquals($expected->dsn,$actual->dsn, 'Incorrect dsn');
    }

    public function testGetConnection() {
        $this->ClearCaches();
        $dbh = \Tops\db\TDatabase::getConnection();
        $this->assertNotNull($dbh);
        $q = $dbh->prepare("SHOW TABLES");
        $q->execute();
        $tables = $q->fetchAll(PDO::FETCH_COLUMN);
        $this->assertNotEmpty($tables);
    }

    public function testGetNativeConnection() {
        $this->ClearCaches();
        $testfile = __DIR__.'/files/classes.ini';
        $tmpClassesFile = __DIR__.'/files/tmp/classes.ini';
        $classesini = __DIR__.'/config/classes.ini';
        $dbini = __DIR__.'/config/database.ini';
        if (file_exists($dbini)) {
            $ini = parse_ini_file($dbini,true);
            $expectedConnections = sizeof(array_keys($ini));
            if (array_key_exists('alias',$ini)) {
                $expectedConnections--;
            }
        }
        else {
            $expectedConnections = 1;
        }

        $restore = (file_exists($classesini));
        if ($restore) {
            copy ($classesini,$tmpClassesFile);
        }
        copy($testfile,$classesini);
        try {
            $actual = \Tops\db\TDatabase::getDbConfigurationForTest();
            self::assertNotEmpty($actual);
            // $this->assertEquals('fakedb',$actual->default);
            $this->assertEquals('dbname',  $actual->connections['fakedb']->dsn);
            $this->assertEquals('username',$actual->connections['fakedb']->user);
            $this->assertEquals('password',$actual->connections['fakedb']->pwd);
            $actualConnections = sizeof($actual->connections);
            self::assertEquals($actualConnections,$expectedConnections);
        }
        finally {
            if ($restore) {
                copy($tmpClassesFile, $classesini);
            }
            else {
                unlink($classesini);
            }
        }
    }

    public function testGetNativeConnectionNoConnectionClass() {
        $this->clearCaches();
        $tmpClassesFile = __DIR__.'/files/tmp/classes.ini';
        $classesini = __DIR__.'/config/classes.ini';
        $dbini = __DIR__.'/config/database.ini';
        if (file_exists($dbini)) {
            $ini = parse_ini_file($dbini,true);
            $expectedConnections = sizeof(array_keys($ini)) - 1;
            if (array_key_exists('alias',$ini)) {
                $expectedConnections--;
            }
        }
        else {
            $expectedConnections = 0;
        }

        $restore = (file_exists($classesini));
        if ($restore) {
            copy ($classesini,$tmpClassesFile);
            unlink($classesini);
        }

        try {
            $actual = \Tops\db\TDatabase::getDbConfigurationForTest();
            self::assertNotEmpty($actual);
            $this->assertFalse(array_key_exists('fakedb',$actual->connections));
            $actualConnections = sizeof($actual->connections);
            self::assertEquals($actualConnections,$expectedConnections);
        }
        finally {
            if ($restore) {
                copy($tmpClassesFile, $classesini);
            }
        }
    }

    public function testGetConfigurationNoDbIni()
    {
        $this->clearCaches();
        $testfile = __DIR__ . '/files/classes.ini';
        $tmpClassesFile = __DIR__ . '/files/tmp/classes.ini';
        $tmpDbFile = __DIR__ . '/files/tmp/database.ini';
        $classesini = __DIR__ . '/config/classes.ini';
        $dbini = __DIR__ . '/config/database.ini';
        $expectedConnections = 1;
        $restoreClassesFile = false;
        copy($dbini, $tmpDbFile);
        unlink($dbini);
        try {
            if (file_exists($classesini)) {
                copy($classesini, $tmpClassesFile);
                $restoreClassesFile = true;
            }
            copy($testfile, $classesini);
            $actual = \Tops\db\TDatabase::getDbConfigurationForTest();
            self::assertNotEmpty($actual);
            $this->assertEquals('fakedb', $actual->default);
            $this->assertEquals('dbname', $actual->connections['fakedb']->dsn);
            $this->assertEquals('username', $actual->connections['fakedb']->user);
            $this->assertEquals('password', $actual->connections['fakedb']->pwd);
            $actualConnections = sizeof($actual->connections);
            self::assertEquals($actualConnections, $expectedConnections);
        } finally {
            if ($restoreClassesFile) {
                copy($tmpClassesFile, $classesini);
            } else {
                unlink($classesini);
            }
            copy($tmpDbFile,$dbini);
        }
    }

    public function testAlias() {
        $this->ClearCaches();
        $dbh = \Tops\db\TDatabase::getConnection('package-db');
        $q = $dbh->prepare("SHOW TABLES");
        $q->execute();
        $tables = $q->fetchAll(PDO::FETCH_COLUMN);
        $this->assertNotEmpty($tables);
        $this->assertNotNull($dbh);
    }

    public function testTableExists() {
        $this->ClearCaches();
        $actusl = \Tops\db\TDatabase::tableExists('no such table');
        $this->assertFalse($actusl);

        $actusl = \Tops\db\TDatabase::tableExists('tops_mailboxes');
        $this->assertTrue($actusl);
    }

    public function testRowCount() {
        $this->ClearCaches();
        $actusl = \Tops\db\TDatabase::rowCount('no such table');
        $this->assertFalse($actusl);

        $actusl = \Tops\db\TDatabase::rowCount('tops_mailboxes');
        $this->assertTrue($actusl !== false);

    }
}
