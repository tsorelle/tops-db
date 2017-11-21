<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/14/2017
 * Time: 6:46 AM
 */

namespace Tops\db\model\repository;
use \PDO;
use PDOStatement;
use Tops\cache\ITopsCache;
use Tops\cache\TSessionCache;
use Tops\db\model\entity\LookupTableEntity;
use Tops\db\TDatabase;
use \Tops\db\TNamedEntitiesRepository;
use Tops\sys\TLanguage;
use Tops\sys\TObjectContainer;
use Tops\sys\TStrings;

class LookupTableRepository extends TNamedEntitiesRepository
{
    private $tableName;
    private $databaseName;
    private $lookupName;
    private $cacheKey;

    const translateItems = true;
    const noTranslation = false;
    const sortByName = 'name';
    const sortByCode = 'code';
    const noSort = '';

    public function __construct($tableName,$database = null)
    {
        $this->tableName = $tableName;
        $this->databaseName = $database;

        $parts = explode('_',$tableName);
        if ($parts > 1) {
            array_shift($parts);
            $this->lookupName = join('',$parts);
        }
        else {
            $this->prefix = 'core';
            $this->lookupName = $tableName;
        }
        $this->lookupName = strtolower($this->lookupName);
        $this->cacheKey = 'lookups.'.strtolower($this->tableName);

    }

    protected function getClassName()
    {
        return '\Tops\db\model\entity\LookupTableEntity';
    }

    protected function getTableName() {
        return $this->tableName;
    }

    protected function getDatabaseId() {
        return $this->databaseName;
    }

    /**
     * @var $cache ITopsCache
     */
    private $cache;

    /**
     * @return ITopsCache || null
     */
    private function getCache() {
        if (!isset($this->cache)) {
            if (TObjectContainer::HasDefinition('tops.lookup.cache')) {
                $this->cache = TObjectContainer::Get('tops.lookup.cache');
            }
            else {
                $this->cache = new TSessionCache();
            }
        }
        return $this->cache;
    }

    public function getLookupList($translate=true,$sort=self::sortByName,$where='',$includeInactive=false)
    {
        // todo: comment out for production optimization
        $this->getCache()->Flush();

        $result = $this->getCache()->Get($this->cacheKey);
        if (!is_array($result)) {
            /**
             * @var $result LookupTableEntity[];
             */
            $result = array();
            $clauses = empty($sort) ? '' : 'ORDER BY '.$sort;
            $result = $this->getListing($where,$includeInactive,$clauses);
            $count = sizeof($result);
            for ($i = 0; $i < $count; $i++) {
                $item = $result[$i];
                if ($item->description === null) {
                    $result[$i]->description = '';
                }
                if ($translate) {
                    if ($item->name === $item->description || $item->description=='') {
                        $item->description = null;
                    }
                    $translateCode = 'lookup-' . $this->lookupName . '-' . strtolower(str_replace(' ', '-', $item->code));
                    $name = TLanguage::text($translateCode . '-name', $item->name);
                    $result[$i]->name = $name;
                    $result[$i]->description =  ($item->description == null) ?
                        $name :
                        TLanguage::text($translateCode . '-description', $item->description);
                }
            }
            $this->getCache()->Set($this->cacheKey, $result);
        }
        return $result;
    }

}