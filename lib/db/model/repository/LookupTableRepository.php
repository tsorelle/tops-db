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

    public function __construct($tableName,$database = null)
    {
        $this->tableName = $tableName;
        $this->databaseName = $database;

        $parts = explode('_',$tableName);
        if ($parts > 1) {
            array_shift($parts);
            $this->lookupName = join('_',$parts);
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

    public function getLookupList($translate=true)
    {
        $result = $this->getCache()->Get($this->cacheKey);
        if (!is_array($result)) {
            /**
             * @var $result LookupTableEntity[];
             */
            $result = array();
            $result = $this->getAll();
            $count = sizeof($result);
            for ($i = 0; $i < $count; $i++) {
                $item = $result[$i];
                if ($item->description === null) {
                    $result[$i]->description = '';
                }
                if ($translate) {
                    $translateCode = 'lookup-' . $this->lookupName . '-' . strtolower(str_replace(' ', '-', $item->code));
                    $result[$i]->name = TLanguage::text($translateCode . '-name', $item->name);
                    $result[$i]->description = TLanguage::text($translateCode . '-description', $item->name);
                }
            }
            $this->getCache()->Set($this->cacheKey, $result);
        }
        return $result;
    }

}