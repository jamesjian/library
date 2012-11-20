<?php
namespace Zx;
use \Zx\Test\Test;
// this autoloader is for Zx classes such as \Zx\Session\Session::test();
class Loader {

    public static function find_file($class) {
        $arr = explode('\\', $class);
        switch ($arr[0]) {
            case 'Zx':
                unset($arr[0]);  //remove 'Zx' because SYSTEM_PATH contain 'Zx'
                $file = strtolower(implode('/', $arr));
                $file_name = SYSTEM_PATH . $file . '.php';
                if (file_exists($file_name)) {
                    include_once $file_name;
                }
                break;
            case APP_NAMESPACE:
                unset($arr[0]);  // because SYSTEM_PATH contain 'Zx'
                $file = strtolower(implode('/', $arr));
                $file_name = APPLICATION_PATH . $file . '.php';
                //Test::object_log('$file_name', $file_name, __FILE__, __LINE__, __CLASS__, __METHOD__);
                
                if (file_exists($file_name)) {
                    include_once $file_name;
                }
                break;
        }
    }

}

spl_autoload_register(__NAMESPACE__ . '\Loader::find_file');
