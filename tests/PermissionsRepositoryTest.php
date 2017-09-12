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

class PermissionsRepositoryTest extends \TwoQuakers\testing\RepositoryTestFixture
{
    /**
     * @var Tops\db\model\repository\PermissionsRepository
     */
    private $repository;

    public function setUp() {
        $this->runSqlScript('new-permissions-tables');
        $this->repository = EntityRepositoryFactory::Get('permissions','Tops\\db\\model\\repository');
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
         * @var $permission \Tops\sys\TPermission
         */
        // $permission = $this->repository->getFirst("permissionName='update mailboxes'");
        $permission = $this->repository->getPermission("update mailboxes");
        $this->assertNotEmpty($permission);
        $expected = 'Manage mailbox list';
        $actual = $permission->getDescription();
        $this->assertEquals($expected,$actual);

        $actual = $permission->check('administrator');
        $this->assertTrue($actual,'admin should have permission');

        $actual = $permission->check('qnut_admin');
        $this->assertTrue($actual,'qnut_admin should have permission');

        $permission = $this->repository->getPermission("add mailbox");
        $this->assertNotEmpty($permission);
        $expected = 'Add a mailbox';
        $actual = $permission->getDescription();
        $this->assertEquals($expected,$actual);

        $actual = $permission->check('administrator');
        $this->assertTrue($actual,'admin should have permission');

        $actual = $permission->check('qnut_admin');
        $this->assertFalse($actual,'qnut_admin should not have permission');

    }

    public function testAddRevokePermission() {
        $roleName = 'test-role';
        $permissionName = 'add mailbox';
        $this->repository->assignPermission($roleName,$permissionName);
        $permission = $this->repository->getPermission($permissionName);
        $roles = $permission->getRoles();
        $this->assertTrue(in_array($roleName,$roles),'Failed assign');

        $this->repository->revokePermission($roleName,$permissionName);
        $permission = $this->repository->getPermission($permissionName);
        $roles = $permission->getRoles();
        $this->assertFalse(in_array($roleName,$roles),'Failed revoke');
    }



}
