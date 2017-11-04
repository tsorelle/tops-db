<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/4/2017
 * Time: 6:02 AM
 */

namespace Tops\db;


use Tops\db\model\repository\TranslationsRepository;
use Tops\sys\TLanguage;
use Tops\sys\TPath;

class TDbTranslator extends TLanguage
{
    private $repository;
    private function getRepository() {
        if (!isset($this->repository)) {
            $this->repository = new TranslationsRepository();
        }
        return $this->repository;
    }

    /**
     * @param $resourceCode
     * @param null $defaultText
     * @return bool|string
     */
    protected function getTranslation($resourceCode, $defaultText = false)
    {
        return $this->getRepository()->getTranslation($this->getLanguages(), $resourceCode);
    }

    private $languages;
    public function getSupportedLanguages()
    {
        if (!isset($this->languages)) {
            $this->languages = $this->getRepository()->getSupportedLanguages();
        }
        return $this->languages;
    }

    /**
     * @param $iniFilePath
     * @param string $username
     * @return int number imported
     */
    public function importTranslations($ini=null,$username='admin')
    {
        $count = 0;
        if ($ini === 'core') {
            $import = $this->getCoreTranslations();
        }
        else if ($ini === null) {
            $import = $ini;
        }
        else {
            $import = @parse_ini_file($ini, true);
        }
        if (!empty($import)) {
            $repository = $this->getRepository();
            foreach ($import as $language => $translations) {
                foreach ($translations as $code => $text) {
                    if (!empty($text)) {
                        $count += $repository->import($language,$code,$text,$username);
                    }
                }
            }
        }
        return $count;
    }

    public static function Import($iniFilePath,$username='admin') {
        $translator = new TDbTranslator();
        return $translator->importTranslations($iniFilePath,'admin');
    }

    public static function ImportCoreTranslations($username = 'admin') {
        $translator = new TDbTranslator();
        $count = $translator->importTranslations('core',$username);
        $usertranslations = TPath::fromFileRoot('application/config/translations.ini');
        $count += $translator->importTranslations($usertranslations);
        return $count;
    }
}