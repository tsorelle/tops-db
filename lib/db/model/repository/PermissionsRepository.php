<?php 
/** 
 * Created by /tools/create-model.php 
 * Time:  2017-09-11 11:21:46
 */ 
namespace Tops\db\model\repository;


use \PDO;
use PDOStatement;
use Tops\db\TEntityRepository;
use Tops\sys\TPermissionsManager;
use Tops\sys\TPermission;
use Tops\sys\TStrings;

class PermissionsRepository extends TEntityRepository 
{
    private function formatKey($name)
    {
        return TStrings::ConvertNameFormat($name,TStrings::dashedFormat);
    }

    private function formatDescription($name) {
        return TStrings::ConvertNameFormat($name,TStrings::initialCapFormat);
    }

    protected function getClassName() {
        return 'Tops\sys\TPermission';
    }

    protected function getDetailTableName() {
        return 'tops_rolepermissions';
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

    public function getPermissionRoles($permission) {
        $id = $permission->getId();

        $dbh = $this->getConnection();
        $sql = 'SELECT roleName FROM '.$this->getDetailTableName().' WHERE permissionId=?';
        /**
         * @var PDOStatement
         */
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    public function getPermission($permissionName) {
        $permissionName = $this->formatKey($permissionName);

        /**
         * @var $permission TPermission
         */
        $permission = $this->getEntity($permissionName);
        if (empty($permission)) {
            return false;
        }
        $roles = $this->getPermissionRoles($permission);
        foreach ($roles as $role) {
            $permission->addRole($role[0]);
        }
        return $permission;
    }

    public function assignPermission($roleName, $permissionName) {
        $permissionName = $this->formatKey($permissionName);
        $roleName = $this->formatKey($roleName);
        $permission = $this->getPermission($permissionName);
        if (empty($permission)) {
           return false;
        }
        if (!in_array($roleName,$permission->getRoles())) {
            $sql = "INSERT INTO ".$this->getDetailTableName().' (permissionId,roleName) VALUES   (?,?)';
            $stmt = $this->executeStatement($sql,array($permission->getId(),$roleName));
        }
        return true;
    }

    public function revokePermission($roleName, $permissionName) {
        $roleName = $this->formatKey($roleName);
        $permission = $this->getPermission($permissionName);
        if (empty($permission)) {
            return false;
        }
        if (in_array($roleName,$permission->getRoles())) {
            $sql = "DELETE FROM ".$this->getDetailTableName()." WHERE permissionId = ? and roleName = ?";
            $this->executeStatement($sql,array($permission->getId(),$roleName));
        }
        return true;
    }

    public function addPermission($permissionName,$description=null,$username='admin') {
        $permission = new TPermission();
        if (empty($description)) {
            $description = $permissionName;
        }

        $permission->setPermissionName($this->formatKey($permissionName));
        $permission->setDescription($this->formatDescription($description));
        $this->insert($permission,$username);
    }

    /**
     * @param string $roleName
     * @return bool
     */
    public function removeRole($roleName) {
        $roleName = $this->formatKey($roleName);
        $sql = 'delete from '.$this->getDetailTableName().' where roleName = ?';
        $this->executeStatement($sql,[$roleName]);
        return true;
    }

    /**
     * @return string[]
     */
    public function getRoles() {
        $sql = 'select distinct roleName from '.$this->getDetailTableName();
        $stmt = $this->executeStatement($sql);
        $stmt->setFetchMode(PDO::FETCH_OBJ);
        $roles = $stmt->fetchAll();
        $result = [];
        foreach ($roles as $role) {
            $result[] = $role->roleName;
        }
        return $result;
    }

    public function removeRolePermissions($roleName) {
        $roleName = $this->formatKey($roleName);
        $sql = 'delete from '.$this->getDetailTableName().' where roleName = ?';
        $this->executeStatement($sql,[$roleName]);
    }

    public function removePermission($permissionName) {
        $permission = $this->getPermission($permissionName);
        if (empty($permission)) {
            return false;
        }
        $id = $permission->getId();
        $sql = 'delete from '.$this->getDetailTableName().' where permissionId = ?';
        $this->executeStatement($sql,[$id]);
        $sql = 'delete from '.$this->getTableName().' where id = ?';
        $this->executeStatement($sql,[$id]);
    }

    public function delete($id)
    {
        $sql = 'delete from '.$this->getDetailTableName().' where permissionId = ?';
        $this->executeStatement($sql,[$id]);
        return parent::delete($id);
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