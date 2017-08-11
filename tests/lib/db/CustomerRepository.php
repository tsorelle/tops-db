<?php 
/** 
 * Created by /tools/create-model.php 
 * Time:  2017-07-03 21:42:06
 */ 

namespace TwoQuakers\testing\db;

use \PDO; 
class CustomerRepository 
{
    public static function Get($id) { 
        $dbh = Database::getConnection();
        $sql = "SELECT * FROM customers WHERE id = ?";
        /** 
         * @var PDOStatement 
         */ 
        $stmt = $dbh->prepare($sql); 
        $stmt->execute(array($id)); 
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'TwoQuakers\testing\model\Customer'); 
        $result = $stmt->fetch(); 
        return $result; 
    } 
 
    public static function Update($dto, $userName = 'admin') { 
        $dbh = Database::getConnection(); 
        $sql = 
            "UPDATE customers SET ".
            "id = :id, \n".
"customertypeid = :customertypeid, \n".
"name = :name, \n".
"address = :address, \n".
"city = :city, \n".
"state = :state, \n".
"postalcode = :postalcode, \n".
"buyer = :buyer, \n".
"changedby  = :changedby, \n".
"changedon  = :changedon, \n".
"active = :active".
 
        "WHERE id = :id"; 
        $today = new \DateTime();  
        $date = $today->format('Y-m-d H:i:s');  

        /** 
         * @var PDOStatement 
         */ 
        $stmt = $dbh->prepare($sql);  
        
$stmt->bindValue(':id', $dto->id, PDO::PARAM_INT);
$stmt->bindValue(':customertypeid', $dto->customertypeid, PDO::PARAM_INT);
$stmt->bindValue(':name', $dto->name, PDO::PARAM_STR);
$stmt->bindValue(':address', $dto->address, PDO::PARAM_STR);
$stmt->bindValue(':city', $dto->city, PDO::PARAM_STR);
$stmt->bindValue(':state', $dto->state, PDO::PARAM_STR);
$stmt->bindValue(':postalcode', $dto->postalcode, PDO::PARAM_STR);
$stmt->bindValue(':buyer', $dto->buyer, PDO::PARAM_STR);
$stmt->bindValue(':changedby', $userName ,PDO::PARAM_STR	);
$stmt->bindValue(':changedon', $date	  ,PDO::PARAM_STR	);
$stmt->bindValue(':active', $dto->active, PDO::PARAM_STR); 
        $count = $stmt->execute(); 
        $result = $dbh->lastInsertId(); 
        return $result;  
    } 
 
    public static function Create($dto,$userName = 'admin') { 
        $dbh = Database::getConnection(); 
        $sql = "INSERT INTO customers (  id, customertypeid, name, address, city, state, postalcode, buyer, createdby, createdon, changedby, changedon, active) ". 
                "VALUES ( :id, :customertypeid, :name, :address, :city, :state, :postalcode, :buyer, :createdby, :createdon, :changedby, :changedon, :active)"; 

        $today = new \DateTime(); 
        $date = $today->format('Y-m-d H:i:s'); 

        /** 
         * @var PDOStatement 
         */ 
        $stmt = $dbh->prepare($sql); 
        
$stmt->bindValue(':id', $dto->id, PDO::PARAM_INT);
$stmt->bindValue(':customertypeid', $dto->customertypeid, PDO::PARAM_INT);
$stmt->bindValue(':name', $dto->name, PDO::PARAM_STR);
$stmt->bindValue(':address', $dto->address, PDO::PARAM_STR);
$stmt->bindValue(':city', $dto->city, PDO::PARAM_STR);
$stmt->bindValue(':state', $dto->state, PDO::PARAM_STR);
$stmt->bindValue(':postalcode', $dto->postalcode, PDO::PARAM_STR);
$stmt->bindValue(':buyer', $dto->buyer, PDO::PARAM_STR);
$stmt->bindValue(':changedby', $userName ,PDO::PARAM_STR	);
$stmt->bindValue(':changedon', $date	  ,PDO::PARAM_STR	);
$stmt->bindValue(':active', $dto->active, PDO::PARAM_STR);  

        
$stmt->bindValue(':createdby', $userName ,PDO::PARAM_STR	);
$stmt->bindValue(':createdon', $date	  ,PDO::PARAM_STR	);  

        $count = $stmt->execute(); 
        $result = $dbh->lastInsertId(); 
        return $result; 
    } 

    public static function Delete($id) { 
        $dbh = Database::getConnection(); 
        $sql = "DELETE FROM customers WHERE id = ?"; 
        /** 
         * @var PDOStatement 
         */ 
        $stmt = $dbh->prepare($sql); 
        $stmt->execute(array($id)); 
    } 

    public static function GetAll($where = '' ) { 
        $dbh = \Tops\db\TDatabase::getConnection(); 
        $sql = "SELECT * FROM customers"; 
        if ($where) { 
            $sql .= " WHERE $where"; 
        } 

        /** 
         * @var PDOStatement 
         */ 
        $stmt = $dbh->prepare($sql); 
        $stmt->execute(); 

        $result = $stmt->fetchAll(PDO::FETCH_CLASS,'TwoQuakers\testing\model\Customer'); 
        return $result; 
    } 
} 
