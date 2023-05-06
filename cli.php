<?php

use Model\Model;
use Utils\Util;
use Utils\Bcrypt;
use Utils\BuildInPageCSS;

if (php_sapi_name() == "cli") {
    require_once dirname(__FILE__).'/Utils/functions.php';
    switch ($argv[1]) {
        case "modules:reread":
            require_once dirname(__FILE__).'/'.$_ENV['ADMIN_FOLDER_URL']
                .'/modules.php';
            reread();
            break;
        case "generate":
            $tableName = $argv[2];
            $className = str_replace('_', '', Util::ucname($tableName));
            $table = new Model($tableName);
            $tableSchema = $table->schema;
            $properties = '';
            foreach ($tableSchema as $propertyName => $propertyDetails) {
                $data_type = false;
                switch ($propertyDetails['DATA_TYPE']) {
                    case 'int':
                    case 'tinyint':
                    case 'smallint':
                    case 'mediumint':
                    case 'bigint':
                    case 'boolean':
                        $data_type = 'int';
                        break;
                    case 'decimal':
                    case 'float':
                    case 'double':
                    case 'real':
                        $data_type = 'float';
                        break;
                    case 'varchar':
                    case 'text':
                    case 'tinyblob':
                    case 'mediumblob':
                    case 'blob':
                    case 'longblob':
                        $data_type = 'string';
                        break;
                    default:
                        break;
                }
                if ($data_type) {
                    if ($propertyDetails['null'] === 'YES') {
                        $data_type = '?'.$data_type;
                    }
                    $properties .= '        public '.$data_type.' $'
                        .$propertyName.';'.PHP_EOL;
                }
            }
            file_put_contents($_ENV['APP_DIR'].'/Model/'.$className.'.php', '<?php

namespace Model {

    class '.$className.' extends Model
    {
'.$properties.'
        public array $schema = '.str_replace("\n", "\n            ",
                    var_export($tableSchema, true)).';
        
        public function __construct() {
            parent::__construct(\''.$tableName.'\');
        }
    }
}');

            break;
        case "buildcss":
            $buildCss = new BuildInPageCSS($argv[2]);
            break;
        case "buildpass":
            $bcrypt = new Bcrypt(10);
            $pass = $bcrypt->hash($argv[2]);
            debug($pass);
            break;
        default:
            break;
    }
}