<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 5/6/2017
 * Time: 8:49 AM
 */

namespace Tops\db;

class TimeStampedEntity
{
    public $createdby;
    public $createdon;
    public $changedby;
    public $changedon;

    public function setCreateTime($userName = 'admin')
    {
        $today = new \DateTime();
        $date = $today->format('Y-m-d H:i:s');
        $this->createdby = $userName;
        $this->createdon = $date;
        $this->changedby = $userName;
        $this->changedon = $date;
    }

    public function setUpdateTime($userName = 'admin')
    {
        $today = new \DateTime();
        $date = $today->format('Y-m-d H:i:s');
        $this->changedby = $userName;
        $this->changedon = $date;
    }
}