<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/17/2017
 * Time: 8:55 AM
 */

use Tops\db\TVariables;
use PHPUnit\Framework\TestCase;

class TVariablesTest extends TestCase
{
    public function testGetValue() {
        TVariables::Clear();
        $expected = 'en';
        $language = TVariables::Get(TVariables::siteLanguageKey);
        $this->assertEquals($expected,$language);

        $language = TVariables::GetSiteLanguage();
        $this->assertEquals($expected,$language);

        $org = TVariables::GetSiteOrganization();
        TVariables::Clear();
        $actual = TVariables::Get(TVariables::siteOrganizationKey);
        $this->assertEquals($org,$actual);

        // from cache
        $actual = TVariables::Get(TVariables::siteOrganizationKey);
        $this->assertEquals($org,$actual);


    }

}
