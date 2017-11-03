<?php 
/** 
 * Created by /tools/create-model.php 
 * Time:  2017-11-03 21:46:24
 */ 
namespace Tops\db\model\repository;


use \PDO;
use PDOStatement;
use Tops\db\TDatabase;
use \Tops\db\TEntityRepository;

class TranslationsRepository extends \Tops\db\TEntityRepository
{
    protected function getTableName() {
        return 'tops_translations';
    }

    protected function getDatabaseId() {
        return 'tops-db';
    }

    protected function getClassName() {
        return 'Tops\db\model\entity\Translation';
    }

    protected function getFieldDefinitionList()
    {
        return array(
        'id'=>PDO::PARAM_INT,
        'language'=>PDO::PARAM_STR,
        'code'=>PDO::PARAM_STR,
        'text'=>PDO::PARAM_STR,
        'createdby'=>PDO::PARAM_STR,
        'createdon'=>PDO::PARAM_STR,
        'changedby'=>PDO::PARAM_STR,
        'changedon'=>PDO::PARAM_STR,
        'active'=>PDO::PARAM_STR);
    }

    public function getTranslation(array $languages,$code) {
        foreach ($languages as $language) {
            $translation = $this->getSingleEntity('language=? && code=?',array($language,$code));
            if (!empty($translation)) {
                return $translation->text;
            }
        }
        return false;
    }

    public function getSupportedLanguages() {
        $sql = 'SELECT distinct language from '.$this->getTableName().' where active=1';
        $stmt = $this->executeStatement($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);

    }
}