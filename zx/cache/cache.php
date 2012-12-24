<?php
namespace Zx\Cache;
defined('SYSTEM_PATH') or die('No direct script access.');
use Zx\Model\Mysql;
/**
 * Description of cache
  CREATE TABLE IF NOT EXISTS `cache` (
  `cache_name` varchar(255) NOT NULL,
  `cache_value` text COMMENT 'serialized value',
  `date_created1` int(11) DEFAULT NULL COMMENT 'unix timestamp',
  `date_created2` datetime NOT NULL,
  `expire` int(11) DEFAULT NULL COMMENT 'unix timestamp, if 0, never expires',
  PRIMARY KEY (`cache_name`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


  in case 'empty' or 'false' is the value of the cache, use NO_CACHE_DATA as the value
  when there is no value for a particular cache id
 * 
 */
if (!defined('NO_CACHE_DATA')) {
    define('NO_CACHE_DATA', 'NO CACHE DATA');
}

class Cache {

    public static function get_one($cache_name) {
        $sql = "SELECT * FROM cache WHERE cache_name=:cache_name";
        $params = array(':cache_name' => $cache_name);
        return Mysql::select_one($sql, $params);
    }

    /**
     * @param <string> $cache_id
     * @return <mixed> if data exists, return unserialized data
     * else return NO_CACHE_DATA
     */
    public static function get_data($cache_name) {
        $cache = self::get_one($cache_name);
        if ($cache) {
            return $cache['cache_value'];
        } else {
            return NO_CACHE_DATA;
        }
    }

    /**
     * @param <string> $cache_id
     * @param <mixed> $data
     * @param <integer> $expire  unix timestamp, if it's 0, means never expire
     * @return <boolean>
     */
    public static function set_data($cache_name, $cache_value, $expire = 0) {
        if (!empty($cache_name)) {
            $cache = self::get_one($cache_name);
            if ($cache) {
                $sql = "UPDATE cache SET cache_value='$cache_value', expire='$expire' 
                WHERE cache_name='$cache_name'";
            } else {
                //others create a new record or replace session data
                $sql = "INSERT INTO cache SET cache_name='$cache_name', cache_value='$cache_value', 
                expire='$expire'";
            }
            $params = array();
            Mysql::exec($sql, $params);  //update or replace
        } else {
            return false;
        }
    }

}