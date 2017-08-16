<?php 
/** 
 * Created by /tools/create-model.php 
 * Time:  2017-08-16 22:52:43
 */ 

namespace TwoQuakers\testing\model;

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
