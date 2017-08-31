<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/16/2017
 * Time: 6:42 AM
 */

namespace Tops\db;

use \PDO;
use PDOStatement;
use Tops\db\TDatabase;

abstract class TEntityRepository
{

    private $fieldDefinitions;
    protected abstract function getFieldDefinitionList();
    protected abstract function getClassName();
    protected abstract function getTableName();
    protected abstract function getDatabaseId();

    private function getFieldDefinitions()
    {
        if (!isset($this->fieldDefinitions)) {
            $this->fieldDefinitions = $this->getFieldDefinitionList();
        }
        return $this->fieldDefinitions;
    }

    /**
     * @var PDO
     */
    private $connection = null;

    private function getConnection() {
        if ($this->connection != null) {
            return $this->connection;
        }
        return TDatabase::getConnection($this->getDatabaseId());
    }

    public function startTransaction() {
        $this->connection = TDatabase::getPersistentConnection($this->getDatabaseId());
        $this->connection->beginTransaction();
    }

    public function commitTransaction() {
        if ($this->connection !=null) {
            $this->connection->commit();
            $this->connection = null;
        }
    }

    public function rollbackTransaction() {
        if ($this->connection !=null) {
            $this->connection->rollBack();
            $this->connection = null;
        }
    }

    /**
     * @param $id
     * @return \TwoQuakers\testing\model\Customer
     */
    public function get($id) {
        $dbh = $this->getConnection();
        $sql = 'SELECT * '.'FROM '.$this->getTableName().' WHERE id = ?';
        /**
         * @var PDOStatement
         */
        $stmt = $dbh->prepare($sql);
        $stmt->execute(array($id));
        $stmt->setFetchMode(PDO::FETCH_CLASS, $this->getClassName());
        $result = $stmt->fetch();
        return $result;
    }

    public function updateValues($id, array $fields,  $userName = 'admin') {
        $dbh = $this->getConnection();
        $sql = array('UPDATE '.$this->getTableName().' SET');
        $names = array_keys($fields);
        $lastField = sizeof($fields) - 1;
        for ($i=0; $i<= $lastField; $i++) {
            $name = $names[$i];
            $sql[] = "$name = :$name".($i == $lastField? '':',');
        }
        $sql[] = " WHERE id = :id";

        $today = new \DateTime();
        $date = $today->format('Y-m-d H:i:s');

        /**
         * @var PDOStatement
         */
        $stmt = $dbh->prepare(join("\n",$sql));
        $fieldDefinitions = $this->getFieldDefinitions();
        foreach ($fields as $name => $value) {
            switch ($name) {
                case 'createdon':
                    // ignore
                    break;
                case 'createdby':
                    // ignore
                    break;
                case 'changedby':
                    $stmt->bindValue(":$name", $userName, $fieldDefinitions[$name]);
                    break;
                case 'changed0n':
                    $stmt->bindValue(":$name", $date, $fieldDefinitions[$name]);
                    break;
                default:
                    $stmt->bindValue(":$name", $value, $fieldDefinitions[$name]);
                    break;
            }
        }
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $count = $stmt->execute();
        $result = $dbh->errorCode();
        return $result;
    }


    public function update($dto, $userName = 'admin') {
        $updateValues = array();
        foreach ($dto as $name => $value) {
            if ($name != 'id' && $name != 'createdby' && $name != 'createdon') {
                $updateValues[$name] = $value;
            }
        }
        return $this->updateValues($dto->id,$updateValues,$userName);
    }

    public function insert($dto,$userName = 'admin') {
        $dbh = $this->getConnection();
        $sql=array('INSERT '.'INTO '.$this->getTableName().' ( ');
        $fieldDefinitions = $this->getFieldDefinitions();
        $fieldNames = array_keys($fieldDefinitions);
        array_shift($fieldNames); //remove id
        $valuesList = array();
        $lastField = sizeof($fieldNames);
        for ($i=0; $i < $lastField; $i++) {
            $valuesList[] = ':'.$fieldNames[$i];
        }

        $sql = 'INSERT '.'INTO '.$this->getTableName().'(  '
            .join(',',$fieldNames)
            .")\n VALUES ( "
            .join(',',$valuesList)
            .')';

        $today = new \DateTime();
        $date = $today->format('Y-m-d H:i:s');

        /**
         * @var PDOStatement
         */
        $stmt = $dbh->prepare($sql);
        foreach ($dto as $name => $value) {
            switch ($name) {
                case 'id':
                    //ignore
                    break;
                case 'createdon':
                    $stmt->bindValue(":$name", $date	  ,PDO::PARAM_STR	);
                    break;
                case 'createdby':
                    $stmt->bindValue(":$name", $userName ,PDO::PARAM_STR	);
                    break;
                case 'changedby':
                    $stmt->bindValue(":$name", $userName, $fieldDefinitions[$name]);
                    break;
                case 'changed0n':
                    $stmt->bindValue(":$name", $date, $fieldDefinitions[$name]);
                    break;
                default:
                    $stmt->bindValue(":$name", $value, $fieldDefinitions[$name]);
                    break;
            }
        }

        // $count =
        $stmt->execute();
        $result = $dbh->lastInsertId();
        return $result;
    }

    public function delete($id)
    {
        $dbh = $this->getConnection();
        $sql = 'DELETE FROM ' . $this->getTableName() . ' WHERE id = ?';
        /**
         * @var PDOStatement
         */
        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute(array($id));
        return $stmt->errorCode();
    }

    public function remove($id) {
        $dbh = $this->getConnection();
        return $this->updateValues($id,array('active' => 0));
    }

    public function getAll($where = '',$includeInactive=false ) {
        $dbh = $this->getConnection();
        $sql = "SELECT * ".'FROM '.$this->getTableName();
        if ($where) {
            $sql .= " WHERE $where";
        }

        if (!$includeInactive) {
            $sql .= (($where) ? ' AND ' : ' WHERE ').'active=1';
        }

        /**
         * @var PDOStatement
         */
        $stmt = $dbh->prepare($sql);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_CLASS,$this->getClassName());
        return $result;
    }
}