<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/28/2017
 * Time: 10:46 AM
 */

namespace Tops\db;


use Tops\db\EntityRepositoryFactory;
use Tops\db\model\repository\PermissionsRepository;
use Tops\sys\IPermissionsManager;
use Tops\sys\TPermission;

class TDBPermissionsManager implements IPermissionsManager
{
    /**
     * @var PermissionsRepository
     */
    private $repository;


    private function getRepository()
    {
        if (!isset($this->repository)) {
            $this->repository = new PermissionsRepository();
        }
        return $this->repository;
    }

    /**
     * @param string $roleName
     * @return bool
     */
    public function addRole($roleName, $roleDescription = null)
    {

        // not implemented. Roles are added by assignPermission
    }

    /**
     * @param string $roleName
     * @return bool
     */
    public function removeRole($roleName)
    {
        return $this->getRepository()->removeRole($roleName);
    }

    /**
     * @return string[]
     */
    public function getRoles()
    {
        return $this->getRepository()->getRoles();
    }

    /**
     * @return TPermission[]
     */
    public function getPermissions()
    {
        return $this->getRepository()->getAll();

    }

    public function addPermission($name, $description)
    {
        return $this->getRepository()->addPermission($name,$description);
    }

    public function removePermission($name)
    {
        return $this->getRepository()->removePermission($name);

    }

    /**
     * @return TPermission
     */
    public function getPermission($permissionName)
    {
        return $this->getRepository()->getPermission($permissionName);

    }

    /**
     * @param string $roleName
     * @param string $permissionName
     * @return bool
     */
    public function assignPermission($roleName, $permissionName)
    {
        return $this->getRepository()->assignPermission($roleName,$permissionName);

    }

    /**
     * @param string $roleName
     * @param string $permissionName
     * @return bool
     */
    public function revokePermission($roleName, $permissionName)
    {
        return $this->getRepository()->revokePermission($roleName,$permissionName);
    }

    public function verifyPermission($permissionName)
    {
        // TODO: Implement verifyPermission() method.
    }
}