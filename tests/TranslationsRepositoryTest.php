<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/3/2017
 * Time: 5:12 PM
 */

use Tops\db\model\repository\TranslationsRepository;
use PHPUnit\Framework\TestCase;
use TwoQuakers\testing\RepositoryTestFixture;

class TranslationsRepositoryTest extends RepositoryTestFixture
{
    public function setUp() {
        $this->runSqlScript('new-translations-table');
    }

    public function testSupportedLanguages() {
        $repository = new TranslationsRepository();
        $result = $repository->getSupportedLanguages();
        $expected = 4;
        $actual = sizeof($result);
        $this->assertEquals($expected,$actual);
    }

    public function testGetTranslation() {
        $repository = new TranslationsRepository();
        $code = 'hello';
        $languages = ['en-US','en'];
        $expected = 'Hi there';
        $actual = $repository->getTranslation($languages,$code);
        $this->assertEquals($expected,$actual);

        $languages = ['en'];
        $expected = 'Hello';
        $actual = $repository->getTranslation($languages,$code);
        $this->assertEquals($expected,$actual);

        $languages = ['sp-MX','sp'];
        $expected = 'Hola amigo';
        $actual = $repository->getTranslation($languages,$code);
        $this->assertEquals($expected,$actual);

        $languages = ['sp'];
        $expected = 'Hola';
        $actual = $repository->getTranslation($languages,$code);
        $this->assertEquals($expected,$actual);

        $languages = ['sp-ES','sp'];
        $expected = 'Hola';
        $actual = $repository->getTranslation($languages,$code);
        $this->assertEquals($expected,$actual);


    }

}
