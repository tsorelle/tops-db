<?php
/**
 * Created by PhpStorm.
 * User: Terry
 * Date: 7/3/2017
 * Time: 12:51 PM
 */

namespace Tops\db;

/*****
 * Example build script: \tools\create-model.php
 */

use Tops\sys\TConfiguration;
use Tops\sys\TPath;
use \PDO;

class TModelBuilder
{
    /**
     * @var \PDO
     */
    private static $dbh;
    private static $modelsPath;
    private static $dbPath;
    private static $appNamespace;
    private static $prefix;
    private static $overwrite;

    private static function buildSource($tableName,$className=null)
    {
        $dbh = self::$dbh;
        $dbPath = self::$dbPath;
        $modelsPath = self::$modelsPath;

        $date = new \DateTime();

        print "\nBuilding $tableName...";

        $q = $dbh->prepare("DESCRIBE $tableName");
        $q->execute();
        $fields = $q->fetchAll(PDO::FETCH_OBJ);

        if (empty($className)) {
            $className = '';
            $parts = explode('_', $tableName);
            if (sizeof($parts) > 1 && $parts[0] == self::$prefix) {
                array_shift($parts);
            }
            foreach ($parts as $part) {
                $className .= strtoupper(substr($part, 0, 1)) . substr($part, 1);
            }

            $plural = substr($className, strlen($className) - 1);
            if ($plural == 's') {
                $className = substr($className, 0, strlen($className) - 1);
            }
        }

        $updateBindings = "";
        $insertBindings = "";
        $updateFields = array();
        $insertNames = array();
        $insertValues = array();
        $entityProperties = array();


        $isTimestamped = false;
        foreach ($fields as $field) {
            switch ($field->Field) {
                case 'createdby' :
                    $isTimestamped = true;
                    $insertBindings .= "\n\$stmt->bindValue(':createdby', \$userName ,PDO::PARAM_STR	);";
                    break;
                case 'createdon' :
                    $isTimestamped = true;
                    $insertBindings .= "\n\$stmt->bindValue(':createdon', \$date	  ,PDO::PARAM_STR	);";
                    break;
                case 'changedby' :
                    $isTimestamped = true;
                    $updateBindings .= "\n\$stmt->bindValue(':changedby', \$userName ,PDO::PARAM_STR	);";
                    $updateFields[] = "\"changedby  = :changedby";
                    break;
                case 'changedon' :
                    $isTimestamped = true;
                    $updateBindings .= "\n\$stmt->bindValue(':changedon', \$date	  ,PDO::PARAM_STR	);";
                    $updateFields[] = "\"changedon  = :changedon";
                    break;
                default:
                    $entityProperties[] =  '    public $' . $field->Field . ";";
                    $type = explode('(', $field->Type)[0];
                    $type = $type == 'int' ? 'INT' : 'STR';
                    $updateBindings .= "\n\$stmt->bindValue(':$field->Field', \$dto->$field->Field, PDO::PARAM_$type);";
                    $updateFields[] =  "\"$field->Field = :$field->Field";
                    break;
            }

            $insertNames[] = ' '.$field->Field;
            $insertValues[] = " :$field->Field";

        }

        $updateFields = join(", \\n\".\n",$updateFields)."\".\n";
        $insertNames = join(",",$insertNames);
        $insertValues = join(",",$insertValues);

        $superclass = $isTimestamped ? ' extends \Tops\db\TimeStampedEntity' : '' ;
        $entity =
            "<?php \n" .
            "/** \n" .
            " * Created by /tools/create-model.php \n" .
            " * Time:  " . $date->format('Y-m-d H:i:s') . "\n" .
            " */ \n\n" .
            "namespace ".self::$appNamespace."\\model;" . "\n\n" .
            "class $className $superclass \n" .
            "{ \n".
            join("\n",$entityProperties).
            "\n} \n";


        $fullClassName = self::$appNamespace."\\model\\" . $className;

        $repos =
            "<?php \n".
            "/** \n" .
            " * Created by /tools/create-model.php \n" .
            " * Time:  " . $date->format('Y-m-d H:i:s') . "\n" .
            " */ \n\n" .
            "namespace ".self::$appNamespace."\\db;" . "\n\n" .
            'use \PDO; ' . "\n" .
            "class $className" . "Repository \n" .
            "{\n" .
            "    public static function Get(\$id) { \n" .
            "        \$dbh = Database::getConnection();\n" .
            "        \$sql = \"SELECT * FROM $tableName WHERE id = ?\";\n" .
            "        /** \n" .
            "         * @var PDOStatement \n" .
            "         */ \n" .
            "        \$stmt = \$dbh->prepare(\$sql); \n" .
            "        \$stmt->execute(array(\$id)); \n" .
            "        \$stmt->setFetchMode(PDO::FETCH_CLASS, '$fullClassName'); \n" .
            "        \$result = \$stmt->fetch(); \n" .
            "        return \$result; \n" .
            "    } \n" .
            " \n" .
            "    public static function Update(\$dto, \$userName = 'admin') { \n" .
            "        \$dbh = Database::getConnection(); \n" .
            "        \$sql = \n" .
            "            \"UPDATE $tableName SET \".\n" .
            "            $updateFields \n" .
            "        \"WHERE id = :id\"; \n" .
            "        \$today = new \\DateTime();  \n" .
            "        \$date = \$today->format('Y-m-d H:i:s');  \n\n" .
            "        /** \n" .
            "         * @var PDOStatement \n" .
            "         */ \n" .
            "        \$stmt = \$dbh->prepare(\$sql);  \n" .
            "        $updateBindings \n" .
            "        \$count = \$stmt->execute(); \n" .
            "        \$result = \$dbh->lastInsertId(); \n" .
            "        return \$result;  \n" .
            "    } \n" .
            " \n" .
            "    public static function Create(\$dto,\$userName = 'admin') { \n" .
            "        \$dbh = Database::getConnection(); \n" .
            "        \$sql = \"INSERT INTO $tableName ( $insertNames) \". \n" .
            "                \"VALUES ($insertValues)\"; \n\n" .
            "        \$today = new \\DateTime(); \n" .
            "        \$date = \$today->format('Y-m-d H:i:s'); \n\n" .
            "        /** \n" .
            "         * @var PDOStatement \n" .
            "         */ \n" .
            "        \$stmt = \$dbh->prepare(\$sql); \n" .
            "        $updateBindings  \n\n" .
            "        $insertBindings  \n\n" .
            "        \$count = \$stmt->execute(); \n" .
            "        \$result = \$dbh->lastInsertId(); \n" .
            "        return \$result; \n" .
            "    } \n\n" .
            "    public static function Delete(\$id) { \n" .
            "        \$dbh = Database::getConnection(); \n" .
            "        \$sql = \"DELETE FROM $tableName WHERE id = ?\"; \n" .
            "        /** \n" .
            "         * @var PDOStatement \n" .
            "         */ \n" .
            "        \$stmt = \$dbh->prepare(\$sql); \n" .
            "        \$stmt->execute(array(\$id)); \n" .
            "    } \n\n" .
            "    public static function GetAll(\$where = '' ) { \n" .
            "        \$dbh = \\Tops\\db\\TDatabase::getConnection(); \n" .
            "        \$sql = \"SELECT * FROM $tableName\"; \n" .
            "        if (\$where) { \n" .
            "            \$sql .= \" WHERE \$where\"; \n" .
            "        } \n\n" .
            "        /** \n" .
            "         * @var PDOStatement \n" .
            "         */ \n" .
            "        \$stmt = \$dbh->prepare(\$sql); \n" .
            "        \$stmt->execute(); \n\n" .
            "        \$result = \$stmt->fetchAll(PDO::FETCH_CLASS,'$fullClassName'); \n" .
            "        return \$result; \n" .
            "    } \n" .
            "} \n";



        self::writeFile($modelsPath,$className.'.php',$entity);
        self::writeFile($dbPath,$className.'Repository.php',$repos);
        print("\n");
    }

    private static function writeFile($filePath,$classFile, $data)
    {
        print "\nWriting '$classFile'...";

        if (self::$overwrite || !file_exists($filePath.$classFile)) {
            file_put_contents($filePath.$classFile,$data);
        }
        else {
            print "\nFile '$classFile' exists. Skipping...";
        }
    }


    private static function makeDirectory($dirname)
    {
        if (!file_exists($dirname)) {
            mkdir($dirname, 0777);
        }
    }

    public static function Build($config=array()) {
        $databaseKey =  @$config['settings']['databaseKey'];
        $srcRoot=@$config['settings']['sourcePath'];
        $appNamespace = @$config['settings']['namespace'];
        self::$prefix=empty($config['settings']['prefix']) ? '' : $config['settings']['prefix'];
        self::$overwrite=empty($config['settings']['overwrite']) ? false : true;

        $include=$config['tables'];

        if ($srcRoot == null) {
            $appSrc = TConfiguration::getValue('application','locations');
            $srcRoot = TPath::getFileRoot().$appSrc.'/';
        }
        else {

            $srcRoot = TPath::normalize(TPath::getFileRoot().$srcRoot);
            if (substr($srcRoot,-1) !== '/') {
                $srcRoot .= '/';
            }

        }
        if (!file_exists($srcRoot)) {
            throw new \Exception("Application directory '$srcRoot' does not exist");
        }

        self::$appNamespace = $appNamespace == null ? $appNamespace : TConfiguration::getValue('applicationNamespace','services');


        self::$modelsPath = $srcRoot.'model/';
        self::$dbPath = $srcRoot.'db/';
        self::makeDirectory(self::$modelsPath);
        self::makeDirectory(self::$dbPath);
        self::$dbPath = $srcRoot.'db/';
        self::$dbh = TDatabase::getConnection($databaseKey);
        if (substr(self::$appNamespace,0,1) == '\\') {
            self::$appNamespace = substr(self::$appNamespace,1);
        }
        print("Building model\n");
        print ("Entity path: ".self::$modelsPath."\n");
        print("Repository path: ".self::$dbPath."\n");

        $q  = self::$dbh->prepare("SHOW TABLES");
        $q->execute();
        $tables = $q->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            if (array_key_exists($table,$include)) {
                 self::buildSource($table,$include[$table]);
            }
        }

        // var_dump($tables);

        print("\n\nBuild complete.\n");
    }

}