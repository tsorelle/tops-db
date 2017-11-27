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
    private $leftTable;
    private $rightTable;
    private $leftIdField;
    private $rightIdField;
    private $leftClass;
    private $rightClass;
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
        $this->leftTable = $leftTable;
        $this->rightTable = $rightTable;
        $this->leftIdField = $leftIdField;
        $this->rightIdField = $rightIdField;
        $this->leftClass = $leftClass;
        $this->rightClass = $rightClass;
        $this->databaseId = $databaseId;
    }

    protected function getConnection()
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

    public function getAssociated($ownerId, $tableName, $idField, $associationIdField, $fields='*', $fetchMode, $className) {
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
            "SELECT $fields FROM $this->associationTable a ".
            "JOIN $tableName t ON a.$idField = t.id ".
            "WHERE a.$associationIdField = ?";

        $stmt = $this->executeStatement($sql, [$ownerId]);
        if ($className == 'stdclass') {
            $stmt->setFetchMode(PDO::FETCH_OBJ);
        }
        else {
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            $stmt->setFetchMode(PDO::FETCH_CLASS, $className);
        }

        $result = $stmt->fetchAll();
        if (empty($result)) {
            return false;
        }
        return $result;
    }


    public function getRightObjects($ownerId, $fields='*') {
        $fetchMode = $this->rightClass === 'stdclass' ? PDO::FETCH_OBJ : PDO::FETCH_CLASS;
        return $this->getAssociated($ownerId,$this->rightTable,$this->leftIdField,$fields,
            $fetchMode,$this->rightClass);
    }

    public function getLeftObjects($ownerId, $fields='*') {
        $fetchMode = $this->leftClass === 'stdclass' ? PDO::FETCH_OBJ : PDO::FETCH_CLASS;
        return $this->getAssociated($ownerId,$this->leftTable,$this->rightIdField,$fields,
            $fetchMode,$this->leftClass);
    }

    public function getRightIdValues($ownerId) {
        return $this->getAssociated($ownerId,$this->rightTable,$this->leftIdField,'id',
            PDO::FETCH_COLUMN,$this->leftClass);
    }

    public function getLeftIdValues($ownerId) {
        return $this->getAssociated($ownerId,$this->leftTable,$this->rightIdField,'id',
            PDO::FETCH_COLUMN,$this->leftClass);
    }
}