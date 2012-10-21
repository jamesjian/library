<?php
/**
 * it's for PHP 5.2 
 * same as \Zx\Test\Test
 * without NAMESPACE
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);
class Test{
    
    /**
     * @example 
      \Zx\Test\Test::object_log('lob', $lob, __FILE__, __LINE__, __CLASS__, __METHOD__);
	  
	  for pdo prepare statement
	  $query = Mysql::interpolateQuery($sql, $params);
      \Zx\Test\Test::object_log('query', $query, __FILE__, __LINE__, __CLASS__, __METHOD__);
						
     * @param string $title
     * @param string $obj
     * @param string $file
     * @param string $line
     * @param string $class
     * @param string $method
     */
    public static function object_log($title = '', $obj=NULL, $file='', $line='', $class='', $method='') {
        $message = $title . ':' . print_r($obj, true) . '<span style="color:red;">file:' . $file.
                'line:' . $line . 'class:' . $class . 'method:' . $method . '</span><br /><br />';
        
        error_log($message, 3, LOG_FILE);
    }
    
}



