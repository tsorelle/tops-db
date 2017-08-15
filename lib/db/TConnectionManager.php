<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/15/2017
 * Time: 4:50 AM
 */

namespace Tops\db;


abstract class TConnectionManager
{
    abstract public function getNativeConfiguration();
}