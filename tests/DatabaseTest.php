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
    public function testGetDefaultConnectionParams() {
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
        $dbh = \Tops\db\TDatabase::getConnection();
        $this->assertNotNull($dbh);
        $q = $dbh->prepare("SHOW TABLES");
        $q->execute();
        $tables = $q->fetchAll(PDO::FETCH_COLUMN);
        $this->assertNotEmpty($tables);
    }

    public function testGetNativeConnection() {
        \Tops\sys\TObjectContainer::ClearCache();
        $testfile = __DIR__.'/files/classes.ini';
        $tmpfile = __DIR__.'/files/tmp/classes.ini';
        $classesini = __DIR__.'/config/classes.ini';
        $dbini = __DIR__.'/config/database.ini';
        if (file_exists($dbini)) {
            $ini = parse_ini_file($dbini,true);
            $expectedConnections = sizeof(array_keys($ini));
        }
        else {
            $expectedConnections = 1;
        }

        $restore = (file_exists($classesini));
        if ($restore) {
            copy ($classesini,$tmpfile);
        }
        copy($testfile,$classesini);
        try {
            $actual = \Tops\db\TDatabase::getDbConfigurationForTest();
            self::assertNotEmpty($actual);
            $this->assertEquals('fakedb',$actual->default);
            $this->assertEquals('dbname',  $actual->connections['fakedb']['database']);
            $this->assertEquals('username',$actual->connections['fakedb']['user']);
            $this->assertEquals('password',$actual->connections['fakedb']['pwd']);
            $actualConnections = sizeof($actual->connections) - 1;
            self::assertEquals($actualConnections,$expectedConnections);
        }
        finally {
            if ($restore) {
                copy($tmpfile, $classesini);
            }
            else {
                unlink($classesini);
            }
        }
    }

    public function testGetNativeConnectionNoConnectionClass() {
        \Tops\sys\TObjectContainer::ClearCache();
        $tmpfile = __DIR__.'/files/tmp/classes.ini';
        $classesini = __DIR__.'/config/classes.ini';
        $dbini = __DIR__.'/config/database.ini';
        if (file_exists($dbini)) {
            $ini = parse_ini_file($dbini,true);
            $expectedConnections = sizeof(array_keys($ini)) - 1;
        }
        else {
            $expectedConnections = 0;
        }

        $restore = (file_exists($classesini));
        if ($restore) {
            copy ($classesini,$tmpfile);
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
                copy($tmpfile, $classesini);
            }
        }
    }

    /*
    public function testGetCustomers() {
        $actusl = \TwoQuakers\testing\db\CustomerRepository::GetAll();
        $this->assertNotEmpty($actusl);
    }
    */
}
