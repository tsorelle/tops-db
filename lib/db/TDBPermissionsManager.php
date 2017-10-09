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
use Tops\sys\TStrings;

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
        if (!isset($roles)) {
            $this->roles = array();
            $roles = $this->getRepository()->getRoles();
            foreach ($roles as $role) {
                $item = new \stdClass();
                $item ->Key = $role;
                $item ->Text = TStrings::ConvertNameFormat($role,IPermissionsManager::roleNameFormat);
                $item ->Description = TStrings::ConvertNameFormat($role,IPermissionsManager::roleDescriptionFormat);
                $result[] = $item;
            }
        }
        return $this->roles;
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

}