<?php 
/** 
 * Created by /tools/create-model.php 
 * Time:  2017-11-17 14:10:01
 */ 
namespace Tops\db\model\repository;


use \PDO;
use PDOStatement;
use Tops\db\model\entity\VariableEntity;
use Tops\db\TDatabase;
use \Tops\db\TNamedEntitiesRepository;

class VariablesRepository extends \Tops\db\TNamedEntitiesRepository
{
    protected function getTableName() {
        return 'tops_variables';
    }

    protected function getDatabaseId() {
        return 'tops-db';
    }

    protected function getClassName() {
        return 'Tops\db\model\entity\VariableEntity';
    }

    public function getArray() {
        $vars = $this->getAll();
        $result = array();
        /**
         * @var $var VariableEntity
         */
        foreach ($vars as $var) {
            $result[$var->code] = $var->value;
        }
        return $result;
    }

    protected function getFieldDefinitionList()
    {
        return array(
        'id'=>PDO::PARAM_INT,
        'code'=>PDO::PARAM_STR,
        'name'=>PDO::PARAM_STR,
        'value'=>PDO::PARAM_STR,
        'description'=>PDO::PARAM_STR,
        'createdby'=>PDO::PARAM_STR,
        'createdon'=>PDO::PARAM_STR,
        'changedby'=>PDO::PARAM_STR,
        'changedon'=>PDO::PARAM_STR,
        'active'=>PDO::PARAM_STR);
    }
}