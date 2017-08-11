<?php 
/** 
 * Created by /tools/create-model.php 
 * Time:  2017-07-03 21:42:06
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
