<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/31/2017
 * Time: 9:24 AM
 */

use PHPUnit\Framework\TestCase;
use Tops\db\EntityRepositoryFactory;
use Tops\mail\TDbMailboxManager;

class MailboxRepositoryTest extends TestCase
{
    public function testGetMailbox() {
        $manager = new TDBMailboxManager();
        $this->assertNotNull($manager);
        $expected = 'clerk';
        $actual = $manager->findByCode($expected);
        $this->assertNotNull($actual);
        $this->assertEquals($expected,$actual->getMailboxCode());
    }
}
