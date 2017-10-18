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
use Tops\mail\TMailbox;

class TDbMailboxManagerTest extends \TwoQuakers\testing\RepositoryTestFixture
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

    public function testAddMailbox() {
        $manager = new TDBMailboxManager();
        $list = $manager->getMailboxes();
        $this->assertNotEmpty($list);
        $expected = sizeof($list) + 1;
        $manager->addMailbox('test','Test box','test@box.com');
        $list = $manager->getMailboxes();
        $actual = sizeof($list);
        $this->assertEquals($expected,$actual,'no record added');
        $expected = 'test';
        $actual = $manager->findByCode($expected);
        $this->assertNotNull($actual);
        $this->assertEquals($expected,$actual->getMailboxCode());
        $actualCode = $actual->getMailboxCode();
        $this->assertEquals($expected,$actualCode);
    }

    public function testUpdateMailbox() {
        $manager = new TDBMailboxManager();
        $this->assertNotNull($manager);
        $testbox = 'clerk';
        $expected = $testbox;
        $actual = $manager->findByCode($testbox);
        $this->assertNotNull($actual);
        $this->assertEquals($expected,$actual->getMailboxCode());
        $actualCode = $actual->getMailboxCode();
        $this->assertEquals($expected,$actualCode);
        $displayText = $actual->getName();
        $address = $actual->getEmail();
        $id = $actual->getMailboxId();
        $description = $actual->getDescription();
        $newName = 'test name';
        $newAddress = 'changed@test.com';
        $newDescription = 'new description';
        $actual->setName($newName);
        $actual->setEmail($newAddress);
        $actual->setDescription($newDescription);
        $manager->updateMailbox($actual);

        $actual = $manager->findByCode($testbox);
        $this->assertNotNull($actual);
        $this->assertEquals($expected,$actual->getMailboxCode());
        $actualCode = $actual->getMailboxCode();
        $this->assertEquals($expected,$actualCode);
        $this->assertEquals($newName,$actual->getName());
        $this->assertEquals($newAddress,$actual->getEmail());
        $this->assertEquals($newDescription,$actual->getDescription());
    }

    public function testRemoveMailbox() {
        $manager = new TDBMailboxManager();
        $testCode = 'support';
        $list = $manager->getMailboxes();
        $this->assertNotEmpty($list);
        $expected = sizeof($list) - 1;
        $manager->remove($testCode);
        $list = $manager->getMailboxes();
        $actual = sizeof($list);
        $this->assertEquals($expected,$actual);
        /**
         * @var $box TMailbox
         */
        $box = $manager->findByCode($testCode);
        $this->assertNotNull($box);


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
