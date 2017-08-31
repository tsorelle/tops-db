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

    private static function buildSource($tableName,$className=null,$databaseId=null)
    {
        $dbh = self::$dbh;
        $dbPath = self::$dbPath;
        $modelsPath = self::$modelsPath;
        $databaseId = empty($databaseId) ? 'null' : "'$databaseId'";
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

        $entityProperties = array();
        $fieldDefs = array();


        $isTimestamped = false;
        foreach ($fields as $field) {
            $fieldName = $field->Field;
            switch ($field->Field) {
                case 'createdby' :
                    $isTimestamped = true;
                    $fieldDefs[] = "'$fieldName'=>PDO::PARAM_STR";
                    break;
                case 'createdon' :
                    $isTimestamped = true;
                    $fieldDefs[] = "'$fieldName'=>PDO::PARAM_STR";
                    break;
                case 'changedby' :
                    $isTimestamped = true;
                    $fieldDefs[] = "'$fieldName'=>PDO::PARAM_STR";
                    break;
                case 'changedon' :
                    $isTimestamped = true;
                    $fieldDefs[] = "'$fieldName'=>PDO::PARAM_STR";
                    break;
                default:
                    $entityProperties[] =  '    public $' . $field->Field . ";";
                    $type = explode('(', $field->Type)[0];
                    $type = $type == 'int' ? 'INT' : 'STR';
                    $fieldDefs[] = "'$fieldName'=>PDO::PARAM_$type";
                    break;
            }
        }

        $superclass = $isTimestamped ? ' extends \Tops\db\TimeStampedEntity' : '' ;
        $entity =
            "<?php \n" .
            "/** \n" .
            " * Created by /tools/create-model.php \n" .
            " * Time:  " . $date->format('Y-m-d H:i:s') . "\n" .
            " */ \n\n" .
            // "namespace ".self::$appNamespace."\\model;" . "\n\n" .
            "namespace ".self::$appNamespace."\\entity;" . "\n\n" .
            "class $className $superclass \n" .
            "{ \n".
            join("\n",$entityProperties).
            "\n} \n";


        // $fullClassName = self::$appNamespace."\\model\\" . $className;
        $fullClassName = self::$appNamespace."\\entity\\" . $className;

        $code = array(
            "<?php ",
            "/** ",
            " * Created by /tools/create-model.php ",
            " * Time:  " . $date->format('Y-m-d H:i:s'),
            " */ \n" .
            "namespace ".self::$appNamespace."\\repository;\n",
            '',
            'use \PDO;',
            'use PDOStatement;',
            'use Tops\db\TDatabase;',
            'use Tops\db\TEntityRepository;',
            '',
            "class $className" . "Repository extends TEntityRepository ",
            "{",
            "    protected function getClassName() {",
            "        return '$fullClassName';",
            "    }",
            "",
            "    protected function getTableName() {",
            "        return '$tableName';",
            "    }",
            "",
            "    protected function getDatabaseId() {",
            "        return $databaseId;",
            "    }",
            "",
            "    protected function getFieldDefinitionList()",
            "    {",
            "        return array("
            );

            $last = sizeof($fieldDefs);
            $count = 0;
            foreach ($fieldDefs as $def) {
                $count++;
                $code[] = "        $def".($count == $last? ');' : ',');
            }
            $code[] = '    }';
            $code[] = '}';

        $repos = join("\n",$code);

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

        self::$appNamespace = $appNamespace == null ?
            TConfiguration::getValue('applicationNamespace','services') :
            $appNamespace;


        self::$modelsPath = $srcRoot.'entity/';
        self::$dbPath = $srcRoot.'repository/';
        self::makeDirectory(self::$modelsPath);
        self::makeDirectory(self::$dbPath);
        // self::$dbPath = $srcRoot.'db/';
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
                 self::buildSource($table,$include[$table],$databaseKey);
            }
        }

        // var_dump($tables);

        print("\n\nBuild complete.\n");
    }

}