<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 9/13/2017
 * Time: 4:36 PM
 */

use Tops\db\TDbInstaller;
use PHPUnit\Framework\TestCase;

class TDbInstallerTest extends TestCase
{
    public function testTopsSchemaInstall() {
        $installer = new TDbInstaller();
        $actual = $installer->installTopsSchema('tests/files/install/sql');
        $this->assertNotEmpty($actual);
        print_r($actual);
    }
}
