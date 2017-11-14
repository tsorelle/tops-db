<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/14/2017
 * Time: 7:32 AM
 */

use Tops\db\model\repository\LookupTableRepository;
use PHPUnit\Framework\TestCase;

class LookupTableRepositoryTest extends TestCase
{
    public function testLookups() {
        $repository = new LookupTableRepository('qnut_addresstypes');
        $actual = $repository->getLookupList();
        $this->assertNotEmpty($actual);
        $cache = new \Tops\cache\TGlobalCache();
        $this->assertNotEmpty($cache);
        $actual = $cache->Get('lookups.qnut_addresstypes');
        $this->assertNotEmpty($actual);
    }
}
