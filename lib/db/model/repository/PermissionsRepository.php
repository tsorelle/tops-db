<?php 
/** 
 * Created by /tools/create-model.php 
 * Time:  2017-09-11 11:21:46
 */ 
namespace Tops\db\model\repository;


use \PDO;
use PDOStatement;
use Tops\db\TDatabase;
use Tops\db\TEntityRepository;
use Tops\sys\TPermission;

class PermissionsRepository extends TEntityRepository 
{
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

    public function getPermission($permissionName) {

        /**
         * @var $permission TPermission
         */
        $permission = $this->getEntity($permissionName);
        if (empty($permission)) {
            return false;
        }

        $id = $permission->getId();

        $dbh = $this->getConnection();
        $sql = 'SELECT roleName FROM '.$this->getDetailTableName().' WHERE permissionId=?';
        /**
         * @var PDOStatement
         */
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$id]);
        $roles = $stmt->fetchAll();
        foreach ($roles as $role) {
            $permission->addRole($role[0]);
        }
        return $permission;
    }

    public function assignPermission($roleName, $permissionName) {
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

    public function addPermission($permissionName,$description,$username='admin') {
        $permission = new TPermission();
        $permission->setPermissionName($permissionName);
        $permission->setDescription($description);
        $this->insert($permission,$username);
    }


    public function removeRolePermissions($roleName) {
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
}