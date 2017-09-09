<?php 
/** 
 * Created by /tools/create-model.php 
 * Time:  2017-08-31 15:28:30
 */ 

namespace Bookstore\model\entity;

class Customer  extends \Tops\db\TimeStampedEntity 
{ 
    public $id;
    public $customertypeid;
    public $name;
    public $address;
    public $city;
    public $state;
    public $postalcode;
    public $buyer;
    public $active;
} 
