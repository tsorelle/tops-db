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

    /*
    public function testGetCustomers() {
        $actusl = \TwoQuakers\testing\db\CustomerRepository::GetAll();
        $this->assertNotEmpty($actusl);
    }
    */
}
