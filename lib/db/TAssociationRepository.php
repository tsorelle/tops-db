<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 11/27/2017
 * Time: 4:56 AM
 */

namespace Tops\db;


use PDO;
use PDOStatement;

class TAssociationRepository
{
    private $associationTable;
    private $left;
    private $right;
    private $databaseId = null;
    private $connection = null;

    public function __construct(
        $associationTable,
        $leftTable,
        $rightTable,
        $leftIdField,
        $rightIdField,
        $leftClass = 'stdclass',
        $rightClass = 'stdclass',
        $databaseId = null)
    {
        $this->associationTable = $associationTable;
        $this->left = $this->createTableInfo($leftTable,$leftIdField,$leftClass);
        $this->right = $this->createTableInfo($rightTable,$rightIdField,$rightClass);
        $this->databaseId = $databaseId;
    }

    private function createTableInfo($name,$idField,$className) {
        $result = new \stdClass();
        $result->tableName = $name;
        $result->idField = $idField;
        $result->className = $className;
        return $result;
    }

    private function getConnection()
    {
        if ($this->connection != null) {
            return $this->connection;
        }
        return TDatabase::getConnection($this->databaseId);
    }

    /**
     * @param $sql
     * @param array $params
     * @return PDOStatement
     */
    protected function executeStatement($sql, $params = array())
    {
        $dbh = $this->getConnection();
        /**
         * @var PDOStatement
         */
        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    private function getAssociated($ownerId, $itemsTable, $filterTable, $fields='*', $fetchMode=PDO::FETCH_CLASS) {

        if (is_array($fields)) {
            array_map(function($field) {
                return 't.'.$field;
            },$fields);
            $fields = join(',',$fields);
        }
        else {
            $fields = 't.'.$fields;
        }

        $sql =
            "SELECT $fields ".
            "FROM $this->associationTable a  ".
            "JOIN $itemsTable->tableName t ON a.$itemsTable->idField = t.id ".
            "JOIN $filterTable->tableName f ON f.id = a.$filterTable->idField ".
            "WHERE a.$filterTable->idField = ?";

        $stmt = $this->executeStatement($sql,[$ownerId]);

        if ($fetchMode === PDO::FETCH_CLASS) {
            if ($itemsTable->className === 'stdclass') {
                $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            }
            else {
                /** @noinspection PhpMethodParametersCountMismatchInspection */
                $stmt->setFetchMode(PDO::FETCH_CLASS, $itemsTable->className);
                $result = $stmt->fetchAll();
            }
        }
        else {
            $result = $stmt->fetchAll($fetchMode);
        };

        if (empty($result)) {
            return [];
        }
        return $result;
    }


    private function updateAssociations($ownerId, $newValues=[],$itemsTable, $filterTable) {
        $sql = "SELECT $itemsTable->idField as id FROM $this->associationTable WHERE $filterTable->idField = ?";
        $stmt = $this->executeStatement($sql,[$ownerId]);
        $current = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $toDelete = array_filter($current, function($currentId) use($newValues) {
           return (!in_array($currentId,$newValues));
        });

        $toAdd = array_filter($newValues, function($currentId) use($current) {
            return (!in_array($currentId,$current));
        });

        foreach ($toDelete as $itemId) {
            $sql = "DELETE FROM $this->associationTable WHERE $filterTable->idField=? and $itemsTable->idField=?";
            $stmt = $this->executeStatement($sql, [$ownerId,$itemId]);
        }

        foreach ($toAdd as $itemId) {
            $sql = "INSERT INTO  $this->associationTable ($filterTable->idField, $itemsTable->idField) values  (?, ?)";
            $stmt = $this->executeStatement($sql, [$ownerId,$itemId]);
        }

    }

    public function addAssociation($ownerId, $itemId, $itemsTable, $filterTable) {
        $sql = "SELECT $itemsTable->idField as id FROM $this->associationTable WHERE $filterTable->idField = ?";
        $stmt = $this->executeStatement($sql,[$ownerId]);
        $current = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array($itemId, $current)) {
            $sql = "INSERT INTO  $this->associationTable ($filterTable->idField, $itemsTable->idField) values  (?, ?)";
            $stmt = $this->executeStatement($sql, [$ownerId,$itemId]);
        }
    }

    public function removeAssociation($ownerId, $itemId, $itemsTable,$filterTable)
    {
        $sql = "DELETE FROM $this->associationTable WHERE $filterTable->idField=? and $itemsTable->idField=?";
        $stmt = $this->executeStatement($sql, [$itemId, $ownerId]);
    }


    public function removeAll($ownerId, $filterTable)
    {
        $sql = "DELETE FROM $this->associationTable WHERE $filterTable->idField=?";
        $stmt = $this->executeStatement($sql, [$ownerId]);
    }

    public function getRightObjects($ownerId, $fields='*') {
        return $this->getAssociated( $ownerId,$this->right,$this->left, $fields);
    }

    public function getLeftObjects($ownerId, $fields='*') {
        return $this->getAssociated( $ownerId,$this->left,$this->right, $fields);
    }

    public function getRightValues($ownerId,$field='id') {
        return $this->getAssociated( $ownerId,$this->right,$this->left, $field,PDO::FETCH_COLUMN);
    }

    public function getLeftValues($ownerId,$field='id') {
        return $this->getAssociated( $ownerId,$this->left,$this->right, $field,PDO::FETCH_COLUMN);
    }

    public function updateLeftValues($ownerId,$newValues=[]) {
        $this->updateAssociations($ownerId,$newValues,$this->left,$this->right);
    }

    public function updateRightValues($ownerId,$newValues=[]) {
        $this->updateAssociations($ownerId,$newValues,$this->right,$this->left);
    }

    public function addAssociationRight($ownerId,$itemId) {
        $this->addAssociation($ownerId,$itemId,$this->right,$this->left);
    }

    public function addAssociationLeft($ownerId,$itemId) {
        $this->addAssociation($ownerId,$itemId, $this->left,$this->right);
    }

    public function removeAssociationRight($ownerId,$itemId) {
        $this->removeAssociation($ownerId,$itemId, $this->left,$this->right);
    }

    public function removeAssociationLeft($ownerId,$itemId) {
        $this->removeAssociation($ownerId,$itemId,$this->right,$this->left);
    }

    public function removeAllRight($ownerId) {
        $this->removeAll($ownerId, $this->right);
    }

    public function removeAllLeft($ownerId) {
        $this->removeAll($ownerId,$this->left);
    }


}