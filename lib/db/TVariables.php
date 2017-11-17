<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/17/2017
 * Time: 8:13 AM
 */

namespace Tops\db;


use Tops\db\model\repository\VariablesRepository;
use Tops\sys\TObjectContainer;
use Tops\cache\ITopsCache;
use Tops\cache\TSessionCache;

class TVariables
{
    const cacheKey = 'tops.variables';
    const siteLanguageKey = 'site-language';
    const siteOrganizationKey = 'site-org';
    /**
     * @var $instance TVariables
     */
    private static $instance = null;
    private static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new TVariables();
        }
        return self::$instance;
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

    public function __construct()
    {
        $repository = new VariablesRepository();
        $this->getCache()->Set(self::cacheKey,$repository->getArray());
    }

    public function getValue($key) {
        $values = $this->cache->Get(self::cacheKey);
        return  @$values[$key];
    }

    public function clearCache() {
        if (isset($this->cache)) {
            $this->cache->Flush();
        }
    }

    public static function Get($key,$default=false) {
        $value = self::getInstance()->getValue($key);
        return ($value === null && $default !== false) ? $default : $value;
    }

    public static function Clear() {
        if (isset(self::$instance)) {
            self::$instance->clearCache();
            self::$instance = null;
        }
    }

    public static function GetSiteLanguage($default='en') {
        return self::Get(self::siteLanguageKey,$default);
    }

    public static function GetSiteOrganization() {
        return self::Get(self::siteOrganizationKey);
    }


}