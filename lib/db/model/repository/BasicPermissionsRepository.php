<?php 
/** 
 * Created by /tools/create-model.php 
 * Time:  2017-09-11 11:21:46
 */
namespace Tops\db\model\repository;

use \PDO;
use Tops\db\TEntityRepository;
use Tops\sys\TPermissionsManager;
use Tops\sys\TBasicPermission;
use Tops\sys\TStrings;

class BasicPermissionsRepository extends TEntityRepository
{
    protected function getClassName() {
        return 'Tops\sys\TBasicPermission';
    }

    protected function getTableName() {
        return 'tops_permissions';
    }

    protected function getDatabaseId() {
        return 'tops-db';
    }

    protected function getFieldDefinitionList()
    {
        return array(
            'id'=>PDO::PARAM_INT,
            'permissionName'=>PDO::PARAM_STR,
            'description'=>PDO::PARAM_STR,
            'active'=>PDO::PARAM_STR);
    }

    protected function getLookupField() {
        return 'permissionName';
    }

    public function getPermission($permissionName) {

        $permissionName = TStrings::convertNameFormat($permissionName, TStrings::dashedFormat);
        /**
         * @var $permission \Tops\sys\TBasicPermission
         */
        $permission = $this->getEntity($permissionName);
        if (empty($permission)) {
            return false;
        }

        return $permission;
    }

    public function addPermission($permissionName,$description,$username='admin') {
        $permissionName = TStrings::convertNameFormat($permissionName,TStrings::dashedFormat);
        $description = TStrings::convertNameFormat($permissionName,TStrings::initialCapFormat);
        $permission = new TBasicPermission();
        $permission->setPermissionName($permissionName);
        $permission->setDescription($description);
        $this->insert($permission,$username);
    }


    public function removePermission($permissionName) {
        $permission = $this->getPermission($permissionName);
        if (empty($permission)) {
            return false;
        }
        $id = $permission->getId();
        $sql = 'delete from '.$this->getTableName().' where id = ?';
        $this->executeStatement($sql,[$id]);
        return true;
    }

    public function getPermissionsList($showAll = false) {
        $sql="SELECT permissionName, IFNULL(description,'') AS description, active FROM ".$this->getTableName();
        if (!$showAll) {
            $sql .= " WHERE active = 1";
        }

        $stmt = $this->executeStatement($sql);
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $result = $stmt->fetchAll();
        if (empty($result)) {
            return false;
        }
        return $result;
    }
}