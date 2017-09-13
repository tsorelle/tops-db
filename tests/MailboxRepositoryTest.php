<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/31/2017
 * Time: 9:24 AM
 */

use PHPUnit\Framework\TestCase;
use Tops\db\EntityRepositoryFactory;
use Tops\db\model\repository\MailboxesRepository;
use Tops\mail\TDbMailboxManager;

class MailboxRepositoryTest extends \TwoQuakers\testing\RepositoryTestFixture
{
    public function setUp()
    {
        $this->runSqlScript('new-mailboxes-table');
    }

    public function testGetMailbox() {
        $manager = new TDBMailboxManager();
        $this->assertNotNull($manager);
        $expected = 'clerk';
        $actual = $manager->findByCode($expected);
        $this->assertNotNull($actual);
        $this->assertEquals($expected,$actual->getMailboxCode());
        $actualCode = $actual->getMailboxCode();
        $displayText = $actual->getName();
        $address = $actual->getEmail();
        $id = $actual->getMailboxId();
        $description = $actual->getDescription();
        $this->assertEquals($expected,$actualCode);
    }

    public function testGetMailboxList() {

        $manager = new TDBMailboxManager();
        $actual = $manager->getMailboxes();
        $this->assertNotEmpty($actual);
        $expected = sizeof($actual);
        $repository = new MailboxesRepository();
        $unfiltered = $repository->getMailboxList(true);
        $actual = sizeof($unfiltered);
        $this->assertEquals($expected,$actual);

    }
}
