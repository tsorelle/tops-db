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

    public function getEntityByCode($value, $includeInactive)
    {
        return parent::getEntity($value, $includeInactive, 'code');
    }

    public function getListing($where='',$includeInactive=false) {

        $dbh = $this->getConnection();
        $sql =
            $this->addSqlConditionals(
                "SELECT id,`code`,`name`, IF(description IS NULL OR description='',`name`,description) AS description FROM ".
                    $this->getTableName(),
                    $includeInactive,
                    $where);

        /**
         * @var PDOStatement
         */
        $stmt = $dbh->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_CLASS,'Tops\sys\TLookupItem');
        return $result;
    }

    public function getGetKeyValueList($where='',$keyField='code',$includeInactive=false) {
        $dbh = $this->getConnection();
        $sql = $this->addSqlConditionals(
            'SELECT `'.$keyField.'`  AS "Key",`name` AS "Value" FROM '.$this->getTableName(),
            $includeInactive,
            $where);

        /**
         * @var PDOStatement
         */
        $stmt = $dbh->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_CLASS,'Tops\sys\TKeyValuePair');
        return $result;
    }

    public function getGetNameValueList($where='',$includeInactive = false) {
        $dbh = $this->getConnection();
        $sql = $this->addSqlConditionals(
            'SELECT `name` as "Name", id as "Value" FROM '.$this->getTableName(),
            $includeInactive,
            $where);

        /**
         * @var PDOStatement
         */
        $stmt = $dbh->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_CLASS,'Tops\sys\TNameValuePair');
        return $result;
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