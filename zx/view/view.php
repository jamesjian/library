<?php
namespace Zx\View;
class View{
        protected static $use_template=true;
        protected static $template_file_name='';
        protected static $view_file_name='';
        protected static $action_variable_array=array();
        protected static $template_variable_array=array();
        public static function set_template_file($template_file_name)
        {
            self::$template_file_name = $template_file_name;
        }
        public static function set_view_file($view_file_name)
        {
            self::$view_file_name = $view_file_name;
        }
        public static function set_template_var($var_name, $var_value)
        {
            self::$template_variable_array[$var_name] = $var_value;
        }
        public static function set_action_var($var_name, $var_value)
        {
            self::$action_variable_array[$var_name] = $var_value;
        }
        /**
         * 
         * set self::$use_template = true;
         */
        public static function use_template()
        {
            self::$use_template = true;
        }
        /**
         * 
         * set self::$use_template = false;
         */
        public static function do_not_use_template()
        {
            self::$use_template = false;
            
        }        
        /**
         * 
         * @return boolean
         */
        public static function wheather_use_template()
        {
            return self::$use_template;
        }
	public static function get_template_file()
        {
            return self::$template_file_name;
        }
	public static function get_view_file()
        {
            return self::$view_file_name;
        }
        public static function get_template_variables()
        {
            return self::$template_variable_array;
        }
        public static function get_action_variables()
        {
            return self::$action_variable_array;
        }

}