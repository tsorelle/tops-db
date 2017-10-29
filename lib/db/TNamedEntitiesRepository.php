<?php 
/** 
 * Created by /tools/create-model.php 
 * Time:  2017-10-29 11:08:02
 */
namespace Tops\db;


use \PDO;
use PDOStatement;
use PHPUnit\Runner\Exception;
use Tops\db\TDatabase;
use Tops\db\TEntityRepository;

class TNamedEntitiesRepository extends TEntityRepository
{
    protected function getClassName() {
        return 'Tops\db\NamedEntity';
    }

    protected function getTableName() {
        throw new \Exception('Table name function must be overriden in subclass');
    }

    protected function getDatabaseId() {
        return null;
    }

    protected function getFieldDefinitionList()
    {
        return array(
        'id'=>PDO::PARAM_INT,
        'code'=>PDO::PARAM_STR,
        'name'=>PDO::PARAM_STR,
        'description'=>PDO::PARAM_STR,
        'createdby'=>PDO::PARAM_STR,
        'createdon'=>PDO::PARAM_STR,
        'changedby'=>PDO::PARAM_STR,
        'changedon'=>PDO::PARAM_STR,
        'active'=>PDO::PARAM_STR);
    }
}