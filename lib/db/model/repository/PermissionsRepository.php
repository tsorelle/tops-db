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
        'description'=>PDO::PARAM_STR);
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
        $sql = 'SELECT roleName FROM tops_rolepermissions WHERE permissionId=?';
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

    protected function getLookupField()
    {
        return 'permissionName';
    }
}