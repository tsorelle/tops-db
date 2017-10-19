<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/28/2017
 * Time: 10:46 AM
 */

namespace Tops\db;


use Tops\db\model\repository\PermissionsRepository;
use Tops\sys\TPermissionsManager;
use Tops\sys\TPermission;
use Tops\sys\TStrings;

class TDBPermissionsManager extends TPermissionsManager
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
        // override in sub class
        return true;
    }

    /**
     * @param string $roleHandle
     * @return bool
     */
    public function removeRole($roleHandle)
    {
        // if native roles are used, override
        return $this->getRepository()->removeRole($roleHandle);
    }

    private $roles;
    /**
     * @return [];
     *
     * return array of stdClass
     *  interface ILookupItem {
     *     Key: any;
     *     Text: string;
     *     Description: string;
     *   }
     */
    public function getRoles()
    {
        // Usually this will be overridden in sub class
        if (!isset($roles)) {
            $this->roles = array();
            $roleNames = $this->getRepository()->getRoles();
            foreach ($roleNames as $roleName) {
                $this->roles[] = $this->createRoleObject($roleName);
            }
        }
        return $this->roles;
    }

    /**
     * @return TPermission[]
     */
    public function getPermissions()
    {
        /**
         * @var $permissions TPermission[]
         */
        $permissions = $this->getRepository()->getAll();
        foreach($permissions as $permission) {
            $roles = $this->getRepository()->getPermissionRoles($permission);
            foreach ($roles as $role) {
                $permission->addRole($role[0]);
            }
        }
        return $permissions;
    }

    public function addPermission($name, $description=null)
    {
        $existing = $this->getPermission($name);
        if (empty($existing)) {
            if (empty($description)) {
                $description = $name;
            }
            return $this->getRepository()->addPermission(
                $name,$description);
        }
        return false;
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

}