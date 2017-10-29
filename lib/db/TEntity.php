<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 10/28/2017
 * Time: 5:07 PM
 */

namespace Tops\db;


class TEntity extends TimeStampedEntity
{
    public $id = 0;
    public $active = true;

    public function setId($value)
    {
        $this->id = empty($value) ? 0 : $value;
    }
    public function getId() {
        return isset($this->id) ? $this->id : 0;
    }

    public function setActive($value = true) {
        $this->active = !empty($value);
    }

    public function getActive() {
        return !empty($this->active);
    }

    public function assignFromObject($dto) {
        parent::assignFromObject($dto);
        if (isset($dto->id)) {
            $this->setId($dto->id);
        }
        if (isset($dto->active)) {
            $this->setActive($dto->active);
        }
    }
}