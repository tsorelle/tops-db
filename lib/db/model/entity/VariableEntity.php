<?php 
/** 
 * Created by /tools/create-model.php 
 * Time:  2017-11-17 14:10:01
 */

namespace Tops\db\model\entity;

class VariableEntity  extends \Tops\db\NamedEntity
{
    public $value;

    public function assignFromObject($dto)
    {
        parent::assignFromObject($dto);
        if (isset($dto->value)) {
            $this->value = $dto->value;
        }
    }
}