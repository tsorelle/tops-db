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

        $dbh = $this->getConnection();
        /**
         * @var PDOStatement
         */
        $stmt = $dbh->prepare($sql);
        $stmt->execute([$ownerId]);

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
            return false;
        }
        return $result;
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
}