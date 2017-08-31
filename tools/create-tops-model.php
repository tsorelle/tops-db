<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 8/31/2017
 * Time: 8:02 AM
 */
include(__DIR__.'/../vendor/autoload.php');
$projectFileRoot =   str_replace('\\','/', realpath(__DIR__.'/..')).'/';
\Tops\sys\TPath::Initialize($projectFileRoot,'tests/config');
$config = parse_ini_file(__DIR__."/modelbuilder-tops.ini",true);
\Tops\db\TModelBuilder::Build($config);