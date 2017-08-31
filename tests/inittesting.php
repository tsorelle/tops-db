<?php
/**
 * Created by PhpStorm.
 * User: terry
 * Date: 5/14/2017'
 * Time: 6:01 AM
 */
$projectFileRoot =   str_replace('\\','/', realpath(__DIR__.'/..')).'/';
if (!class_exists("\\Tops\\db\\TDatabase")) {
    exit('Autoload failed');
}
\Tops\sys\TPath::Initialize($projectFileRoot,'tests/config');

