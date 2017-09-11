<?php 
/** 
 * Created by /tools/create-model.php 
 * Time:  2017-08-31 15:06:44
 */ 
namespace Tops\db\model\repository;


use \PDO;
use PDOStatement;
use Tops\db\TDatabase;
use Tops\db\TEntityRepository;

class MailboxesRepository extends TEntityRepository 
{
    protected function getClassName() {
        return 'Tops\mail\TMailbox';
    }

    protected function getTableName() {
        return 'tops_mailboxes';
    }

    protected function getDatabaseId() {
        return 'tops-db';
    }

    protected function getFieldDefinitionList()
    {
        return array(
        'id'=>PDO::PARAM_INT,
        'mailboxcode'=>PDO::PARAM_STR,
        'address'=>PDO::PARAM_STR,
        'displayText'=>PDO::PARAM_STR,
        'description'=>PDO::PARAM_STR,
        'createdby'=>PDO::PARAM_STR,
        'createdon'=>PDO::PARAM_STR,
        'changedby'=>PDO::PARAM_STR,
        'changedon'=>PDO::PARAM_STR,
        'active'=>PDO::PARAM_STR,
        'public'=>PDO::PARAM_STR);
    }

    protected function getLookupField() {
        return 'mailboxcode';
    }
}