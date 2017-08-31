<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/16/2017
 * Time: 6:54 AM
 */

use PHPUnit\Framework\TestCase;
use Tops\sys\TPath;

class RepositoryTest extends TestCase
{
    private function ClearCaches() {
        \Tops\sys\TObjectContainer::ClearCache();
        \Tops\db\TDatabase::ClearCache();
    }

    private function runSqlScriipt($script) {
        $this->ClearCaches();
        $token=\Tops\sys\TSession::GetSecurityToken();
        $script = TPath::GetFileRoot()."tests/sql/$script.sql";
        $this->assertTrue(file_exists($script),"Cannot complete test. Sql script '$script' not found.'");
        $result = \Tops\db\TDatabase::ExecuteSql($token,$script);
        return $result;
    }

    public function testGetAllEntities() {
        $this->runSqlScriipt('new-customer-table');
        $repository = new \Bookstore\model\repository\CustomerRepository();
        $actusl = $repository->getAll();
        $this->assertNotEmpty($actusl);
        $actualCount = sizeof($actusl);
        $expectedCount = 2;
        $this->assertEquals($expectedCount,$actualCount);
    }

    public function testTransaction() {
        $this->runSqlScriipt('new-customer-table');
        $repository = new \Bookstore\model\repository\CustomerRepository();

        $customer = new \Bookstore\model\entity\Customer();
        $customer->id = 0;
        $customer->customertypeid = 1;
        $customer->name = 'New Test Customer';
        $customer->address = '1232 Somewhere';
        $customer->city = 'Austin';
        $customer->state = 'Texas';
        $customer->postalcode = '78765';
        $customer->buyer = 'Joe Buyer';
        $customer->active = 1;

        try {
            $repository->startTransaction();
            $repository->insert($customer);
            $customer->city = 'Montgomery';
            $customer->state = 'Alabama';
            $repository->update($customer);
            throw new Exception('test exc');
            // $repository->commitTransaction();
        }
        catch (\Exception $ex) {
            $repository->rollbackTransaction();
        }
    }

    public function testInsertEntity() {
        $this->runSqlScriipt('new-customer-table');
        $customer = new \Bookstore\model\entity\Customer();
        $customer->id = 0;
        $customer->customertypeid = 1;
        $customer->name = 'New Test Customer';
        $customer->address = '1232 Somewhere';
        $customer->city = 'Austin';
        $customer->state = 'Texas';
        $customer->postalcode = '78765';
        $customer->buyer = 'Joe Buyer';
        $customer->active = 1;

        $repository = new \Bookstore\model\repository\CustomerRepository();
        $returnedId = $repository->insert($customer);
        $this->assertTrue($returnedId > 0);
        $actusl = $repository->getAll();
        $actualCount = sizeof($actusl);
        $expectedCount = 3;
        $this->assertEquals($expectedCount,$actualCount);
    }

    public function testGetEntity() {
        $this->runSqlScriipt('new-customer-table');

        $repository = new \Bookstore\model\repository\CustomerRepository();
        $expectedId = 2;
        $expectedCustomertypeid = 1;
        $expectedName = 'Kinder Kindles';
        $expectedAddress = '9032 Main';
        $expectedCity = 'Boston';
        $expectedState = 'MA';
        $expectedPostalcode = '02746';
        $expectedBuyer = '';
        $expectedActive = 1;

        $customer = $repository->get($expectedId);

        $this->assertEquals($expectedId, $customer->id);
        $this->assertEquals($expectedCustomertypeid, $customer->customertypeid);
        $this->assertEquals($expectedName, $customer->name);
        $this->assertEquals($expectedAddress, $customer->address);
        $this->assertEquals($expectedCity, $customer->city);
        $this->assertEquals($expectedState, $customer->state);
        $this->assertEquals($expectedPostalcode, $customer->postalcode);
        $this->assertEquals($expectedBuyer, $customer->buyer);
        $this->assertEquals($expectedActive, $customer->active);

    }

    public function testUpdateEntity() {
        $this->runSqlScriipt('new-customer-table');
        $expectedId = 1;
        $expectedCustomertypeid = 2;
        $expectedName = 'Kids Korner Bookstore';
        $expectedAddress = '3001 Bee Caves Road';
        $expectedCity = 'New Orleans';
        $expectedState = 'LA';
        $expectedPostalcode = '98765';
        $expectedBuyer = 'bob';
        $expectedActive = 1;

        $repository = new \Bookstore\model\repository\CustomerRepository();
        $customer = $repository->get(1);
        $customer->city = $expectedCity;
        $customer->state = $expectedState;
        $customer->postalcode = $expectedPostalcode;

        $errorCode = $repository->update($customer);
        $expected = 0;
        $this->assertEquals($expected,$errorCode);

        $customer = $repository->get(1);

        $this->assertEquals($expectedId, $customer->id);
        $this->assertEquals($expectedCustomertypeid, $customer->customertypeid);
        $this->assertEquals($expectedName, $customer->name);
        $this->assertEquals($expectedAddress, $customer->address);
        $this->assertEquals($expectedCity, $customer->city);
        $this->assertEquals($expectedState, $customer->state);
        $this->assertEquals($expectedPostalcode, $customer->postalcode);
        $this->assertEquals($expectedBuyer, $customer->buyer);
        $this->assertEquals($expectedActive, $customer->active);
    }
    public function testUpdateEntityFields() {
        $this->runSqlScriipt('new-customer-table');
        $expectedId = 1;
        $expectedCustomertypeid = 2;
        $expectedName = 'Kids Korner Bookstore';
        $expectedAddress = '3001 Bee Caves Road';
        $expectedCity = 'New Orleans';
        $expectedState = 'LA';
        $expectedPostalcode = '98765';
        $expectedBuyer = 'bob';
        $expectedActive = 1;

        $repository = new \Bookstore\model\repository\CustomerRepository();
        $updateValues = array(
            'city' => $expectedCity,
            'state' => $expectedState,
            'postalcode' => $expectedPostalcode);

        $errorcode = $repository->updateValues($expectedId,$updateValues);
        $this->assertEquals(0,$errorcode);

        $customer = $repository->get($expectedId);

        $this->assertEquals($expectedId, $customer->id);
        $this->assertEquals($expectedCustomertypeid, $customer->customertypeid);
        $this->assertEquals($expectedName, $customer->name);
        $this->assertEquals($expectedAddress, $customer->address);
        $this->assertEquals($expectedCity, $customer->city);
        $this->assertEquals($expectedState, $customer->state);
        $this->assertEquals($expectedPostalcode, $customer->postalcode);
        $this->assertEquals($expectedBuyer, $customer->buyer);
        $this->assertEquals($expectedActive, $customer->active);

    }
    public function testRemove() {
        $this->runSqlScriipt('new-customer-table');
        $repository = new \Bookstore\model\repository\CustomerRepository();
        $errorcode = $repository->remove(1);
        $this->assertEquals(0,$errorcode);
        $remaining = $repository->getAll();
        $expected = 1;
        $actual = sizeof($remaining);
        $this->assertEquals($expected,$actual);
    }

    public function testDelete() {
        $this->runSqlScriipt('new-customer-table');
        $repository = new \Bookstore\model\repository\CustomerRepository();
        $errorcode = $repository->delete(1); // delete rather than deactivate
        $this->assertEquals(0,$errorcode);
        $remaining = $repository->getAll();
        $expected = 1;
        $actual = sizeof($remaining);
        $this->assertEquals($expected,$actual);

    }
}
