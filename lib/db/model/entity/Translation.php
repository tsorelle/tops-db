<?php 
/** 
 * Created by /tools/create-model.php 
 * Time:  2017-11-03 21:46:24
 */ 

namespace Tops\db\model\entity;

class Translation  extends \Tops\db\TimeStampedEntity 
{ 
    public $id;
    public $language;
    public $code;
    public $text;
    public $active;

     public function assignFromObject($dto) {
    if (isset($dto->id)) {
       $this->id = $dto->id;
    }
    if (isset($dto->language)) {
       $this->language = $dto->language;
    }
    if (isset($dto->code)) {
       $this->code = $dto->code;
    }
    if (isset($dto->text)) {
       $this->text = $dto->text;
    }
    if (isset($dto->active)) {
       $this->active = $dto->active;
    }

} 
}