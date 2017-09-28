<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/11/2017
 * Time: 6:53 AM
 */

use Tops\db\EntityRepositoryFactory;
use Tops\db\model\repository\PermissionsRepository;
use PHPUnit\Framework\TestCase;

class BasicPermissionsRepositoryTest extends \TwoQuakers\testing\RepositoryTestFixture
{
    /**
     * @var Tops\db\model\repository\PermissionsRepository
     */
    private $repository;

    public function setUp() {
        $this->runSqlScript('new-permissions-tables');
        $this->repository = EntityRepositoryFactory::Get('basic-permissions','Tops\\db\\model\\repository');
        $this->assertNotEmpty($this->repository);
    }

    public function testGetPermissions() {

        $actusl = $this->repository->getAll();
        $this->assertNotEmpty($actusl);
        $actualCount = sizeof($actusl);
        $expectedCount = 2;
        $this->assertEquals($expectedCount,$actualCount);
    }

    public function testGetPermission() {
        /**
         * @var $permission \Tops\sys\TBasicPermission
         */
        // $permission = $this->repository->getFirst("permissionName='update mailboxes'");
        $permission = $this->repository->getPermission("update mailboxes");
        $this->assertNotEmpty($permission);
        $expected = 'Manage mailbox list';
        $actual = $permission->getDescription();
        $this->assertEquals($expected,$actual);

        $permission = $this->repository->getPermission("add mailbox");
        $this->assertNotEmpty($permission);
        $expected = 'Add a mailbox';
        $actual = $permission->getDescription();
        $this->assertEquals($expected,$actual);

    }

    public function testAddPermission() {
        $permissionName = 'test permission';
        $description = 'just a test';
        $this->repository->addPermission($permissionName,$description);
        $permission = $this->repository->getPermission($permissionName);
        $this->assertNotEmpty($permission);
    }

    public function testRemovePermission() {
        $permissionName = 'test permission';
        $description = 'just a test';
        $this->repository->addPermission($permissionName,$description);
        $permission = $this->repository->getPermission($permissionName);
        $this->assertNotEmpty($permission);
        $this->repository->removePermission($permissionName);
        $actual = $this->repository->getPermission($permissionName);
        $this->assertFalse($actual,'Delete failed');
    }

    public function testGetPermissionsList() {
        $actual = $this->repository->getPermissionsList();
        $this->assertNotEmpty($actual);
    }


}
