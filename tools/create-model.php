<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 7/3/2017
 * Time: 3:39 PM
 */
include(__DIR__.'/../vendor/autoload.php');
$projectFileRoot =   str_replace('\\','/', realpath(__DIR__.'/..')).'/';
\Tops\sys\TPath::Initialize($projectFileRoot,'tests/config');
$config = parse_ini_file(__DIR__."/modelbuilder.ini",true);
\Tops\db\TModelBuilder::Build($config);