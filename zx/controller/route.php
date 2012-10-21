<?php

namespace Zx\Controller;

use \Zx\Test\Test;

/**
  $query_string = $_SERVER['QUERY_STRING'];   //M1=2&M2=3
  $uri = $_SERVER['REQUEST_URI');  //   /z2/public/xxx/yyy/zzz?M1=2&M2=3
 */
class Route {

    public static $module_controller_action = array('module' => '', 'controller' => '', 'action' => '');
    public static $params = array();
    public static $url = ''; //remember current url

    public static function get_modules() {
        $ini_array = parse_ini_file(APPLICATION_PATH . 'config/module.php');
        return $ini_array['module'];
    }

    public static function get_routes() {
        $ini_array = parse_ini_file(APPLICATION_PATH . 'config/route.php');
        $route_configs = $ini_array['route'];
        //\Zx\Test\Test::object_log('$route_configs', $route_configs, __FILE__, __LINE__, __CLASS__, __METHOD__);

        $routes = array();
        foreach ($route_configs as $route_config) {
            $arr = explode(',', $route_config);
            //  \Zx\Test\Test::object_log('$arr', $arr, __FILE__, __LINE__, __CLASS__, __METHOD__);
            //\Zx\Test\Test::object_log('$arr', $arr[1], __FILE__, __LINE__, __CLASS__, __METHOD__);

            $routes[$arr[0]] = $arr[1]; //about-us.php->front/article/about_us
        }
        return $routes;
    }

    public static function get_url() {
        return self::$url;
    }

    public static function analyze_url() {
        self::$url = $_SERVER['REQUEST_URI'];
        $url = urldecode(substr(self::$url, strlen(URL_PREFIX))); //remove prefix such as "/pree/" in test server or "/" in live site
        //\Zx\Test\Test::object_log('decode url', urldecode($url), __FILE__, __LINE__, __CLASS__, __METHOD__);

        $arr = explode('/', $url);
        $length = count($arr); //it's always >1, even if no query string at all, $arr contain an empty string element
        //\Zx\Test\Test::object_log('$arr', $arr, __FILE__, __LINE__, __CLASS__, __METHOD__);
        //\Zx\Test\Test::object_log('$arr', $length, __FILE__, __LINE__, __CLASS__, __METHOD__);
        if ($length < 3) {
            switch ($length) {
                case 0:
                //it's always not 0
                case 1:
                    //special static page,  about-us.php
                    $original_name = trim($arr[0]);
                    if ($original_name == '') {
                        //\Zx\Test\Test::object_log('$arr', '11111', __FILE__, __LINE__, __CLASS__, __METHOD__);
                        //if no query string, goto home page
                        self::$module_controller_action = array(
                            'module' => 'front',
                            'controller' => 'common',
                            'action' => 'home',
                        );
                    } else {
                        //\Zx\Test\Test::object_log('$arr', '22222', __FILE__, __LINE__, __CLASS__, __METHOD__);
                        $routes = self::get_routes();
                        //\Zx\Test\Test::object_log('$original_name', $original_name, __FILE__, __LINE__, __CLASS__, __METHOD__);
                        //\Zx\Test\Test::object_log('$routes', $routes, __FILE__, __LINE__, __CLASS__, __METHOD__);
                        if (array_key_exists($original_name, $routes)) {
                            $route = $routes[$original_name];
                            $arr = explode('/', $route);
                            self::$module_controller_action = array(
                                'module' => trim($arr[0]), //maybe have empty space
                                'controller' => trim($arr[1]),
                                'action' => trim($arr[2]),
                            );
                            //\Zx\Test\Test::object_log('$arr', self::$module_controller_action, __FILE__, __LINE__, __CLASS__, __METHOD__);
                        }
                    }
                    break;
                case 2:
                //todo:
            }
        } else {
            //module/controller/action/params
            //currently remove /pree/public/
            self::$module_controller_action = array(
                'module' => $arr[0],
                'controller' => $arr[1],
                'action' => $arr[2],
            );
            for ($i = 3; $i < $length; $i++) {
                self::$params[] = $arr[$i];
            }
        }
        //Test::object_log('$module_controller_action', self::$module_controller_action, __FILE__, __LINE__, __CLASS__, __METHOD__);
        //\Zx\Test\Test::object_log('$arr', self::$module_controller_action, __FILE__, __LINE__, __CLASS__, __METHOD__);
        self::set_module();
        //\Zx\Test\Test::object_log('$arr', self::$module_controller_action, __FILE__, __LINE__, __CLASS__, __METHOD__);
        self::set_controller();
        //\Zx\Test\Test::object_log('$arr', self::$module_controller_action, __FILE__, __LINE__, __CLASS__, __METHOD__);
        self::set_action();
        //\Zx\Test\Test::object_log('$arr', self::$module_controller_action, __FILE__, __LINE__, __CLASS__, __METHOD__);
        //self::set_params();
    }

    /**
      module must be defined in config/module.php
      if module does not exist, go to error
     */
    public static function set_module() {
        $modules = self::get_modules();
        //\Zx\Test\Test::object_log('$modules', $modules, __FILE__, __LINE__, __CLASS__, __METHOD__);

        $module = self::$module_controller_action['module'];
        //\Zx\Test\Test::object_log('$module', $module, __FILE__, __LINE__, __CLASS__, __METHOD__);

        if (!in_array($module, $modules)) {
            //\Zx\Test\Test::object_log('$module', 'no', __FILE__, __LINE__, __CLASS__, __METHOD__);

            self::set_page_not_exist_url();
        }
    }

    public static function set_page_not_exist_url() {
        $module = 'error';
        $controller = 'index';
        $action = 'page_not_exist';
        self::$module_controller_action = array(
            'module' => $module,
            'controller' => $controller,
            'action' => $action,
        );
        self::$params = array();
    }

    public static function get_module() {
        if (self::$module_controller_action['module'] == '') {
            self::analyze_url();
        }
        return self::$module_controller_action['module'];
    }

    /**
      controller class file must exist in module folder
      if controller does not exist, go to error
     */
    public static function set_controller() {
        $module = self::get_module();
        $controller_file_path = APPLICATION_PATH . 'module/' . $module . '/controller/' .
                self::$module_controller_action['controller'] . '.php';
        if (!file_exists($controller_file_path)) {
            \Zx\Test\Test::object_log('controller', 'no', __FILE__, __LINE__, __CLASS__, __METHOD__);
            self::set_page_not_exist_url();
        }
    }

    public static function get_controller() {
        if (self::$module_controller_action['controller'] == '') {
            return self::analyze_url();
        }
        return self::$module_controller_action['controller'];
    }

    /**
      at this moment, controller class must exist
     */
    public static function get_controller_class() {
        $module = self::get_module();
        $controller = self::get_controller();
        $controller_file_path = APPLICATION_PATH . 'module/' . $module . '/controller/' .
                $controller . '.php';
        $controller_class_name = '\\App\\Module\\' . ucfirst($module) . '\\Controller\\' . ucfirst($controller);
        include_once $controller_file_path;
        return $controller_class_name;
    }

    /**
      action method must exist in controller class file
      if action does not exist, go to error
     */
    public static function set_action() {
        $controller_class = self::get_controller_class();
        $action = self::$module_controller_action['action'];

        if (!method_exists($controller_class, $action)) {
            //\Zx\Test\Test::object_log('action', 'no', __FILE__, __LINE__, __CLASS__, __METHOD__);
            self::set_page_not_exist_url();
        }
    }

    public static function get_action() {
        if (self::$module_controller_action['action'] == '') {
            return self::analyze_url();
        }
        return self::$module_controller_action['action'];
    }

    /**

     */
    public static function get_params() {
        return self::$params;
    }

    /**
      params have been set in self::analyze_url()
     */
    public static function set_params() {
        
    }

}