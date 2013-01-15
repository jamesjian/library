<?php

namespace Zx\Model;

defined('SYSTEM_PATH') or die('No direct script access.');
/**
 * Note:
 * if not used prepared statement to get param, make sure quote all the parameters before query.
 * For example:
 * 
        $dbh = Mysql::get_dbh();
        $title = $dbh->quote(strtolower($title));
        $sql = sprintf(" SELECT * FROM  ad WHERE title=%s",$title);
 *      Mysql::select_one($sql);
 * 
 * Otherwise it will report mysql error message if has some special characters such as single quotes.
 */

class Mysql {

    protected static $dbh;
    public static function get_dbh()
    {
        if (!isset(self::$dbh)) {
            self::connect_db();
        }
        return self::$dbh;
    }
    public static function connect_db() {
        if (isset(self::$dbh)) {
            return self::$dbh;
        } else {
            $dsn = 'mysql:dbname=' . DBNAME . ';host=' . DBHOST;
            try {
                self::$dbh = new \PDO($dsn, DBUSER, DBPASS);
                $sql = "SET NAMES UTF8";
                $sth = self::$dbh->prepare($sql);
                $sth->execute();
                self::$dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );      //for debug          
                return self::$dbh;
            } catch (\PDOException $e) {
                \Zx\Test\Test::object_log('$e->getMessage()', $e->getMessage(), __FILE__, __LINE__, __CLASS__, __METHOD__);

                die('Sorry, something wrong with the site, please try it later!');
            }
        }
    }

    /**
     * 
     * @param string $sql INSERT INTO session SET session_id=:id, session_data=:data, expires=:time
     * @param array $params array(':id' => $id, ':data' => $data, ':time' => $time)
     * @return int
     */
    public static function insert($sql, $params = array()) {
        //\Zx\Test\Test::object_log('$sql', $sql, __FILE__, __LINE__, __CLASS__, __METHOD__);
        //\Zx\Test\Test::object_log('$params', $params, __FILE__, __LINE__, __CLASS__, __METHOD__);
        $dbh = self::connect_db();
        try {
            $sth = $dbh->prepare($sql);
            /* no need to quote again, if parameters are in $params array, 
             * it will be handled by PHP
            $quoted_params = array();
            foreach ($params as $column_name=>$column_value) {
                $quoted_params[$column_name] = $dbh->quote($column_value);
            }
            $sth->execute($quoted_params);
             * 
             */
            $sth->execute($params);
        } catch (PDOException $e) {
            //  \Zx\Test\Test::object_log('$e->getMessage()', $e->getMessage(), __FILE__, __LINE__, __CLASS__, __METHOD__);
            die('Sorry, something wrong with the site, please try it later!');
        }
        return $dbh->lastInsertId();
    }

    /**
     * only for update and delete
     * @param string $sql 
     * @param array $params 
     * @return number of rows affected by the last SQL statement
     */
    public static function exec($sql, $params = array()) {
        //\Zx\Test\Test::object_log('$sql', $sql, __FILE__, __LINE__, __CLASS__, __METHOD__);
        //\Zx\Test\Test::object_log('$params', $params, __FILE__, __LINE__, __CLASS__, __METHOD__);
        $dbh = self::connect_db();
        try {
            $sth = $dbh->prepare($sql);
            $sth->execute($params);
        } catch (PDOException $e) {
            \Zx\Test\Test::object_log('$e->getMessage()', $e->getMessage(), __FILE__, __LINE__, __CLASS__, __METHOD__);
            die('Sorry, something wrong with the site, please try it later!');
        }
        //return $sth->rowCount(); //may be 0 if nothing to delete or update
        return true;
    }

    /**
     * 
     * @param string $sql  SELECT session_data FROM session WHERE session_id=:id AND expires>:time
     * @param array $params  array(':id' => $id, ':time' => $time)
     * @return 2D array or boolean when false
     */
    public static function select_all($sql, $params = array()) {
        $dbh = self::connect_db();
        try {
            $sth = $dbh->prepare($sql);
            $sth->execute($params);
            $r = $sth->fetchAll();
        } catch (PDOException $e) {
            \Zx\Test\Test::object_log('$e->getMessage()', $e->getMessage(), __FILE__, __LINE__, __CLASS__, __METHOD__);
            die('Sorry, something wrong with the site, please try it later!');
        }
        return $r;
    }

    /**
     * 
     * @param string $sql  SELECT session_data FROM session WHERE session_id=:id AND expires>:time
     * @param array $params  array(':id' => $id, ':time' => $time)
     * @return 1D array or boolean when false
     */
    public static function select_one($sql, $params = array()) {
        
        //\Zx\Test\Test::object_log('$sql', $sql, __FILE__, __LINE__, __CLASS__, __METHOD__);
        //\Zx\Test\Test::object_log('$params', $params, __FILE__, __LINE__, __CLASS__, __METHOD__);

        $dbh = self::connect_db();
        try {
            $sth = $dbh->prepare($sql);
            $r = $sth->execute($params);
            if ($r) {
                //\Zx\Test\Test::object_log('$r1', 'true', __FILE__, __LINE__, __CLASS__, __METHOD__);
            } else {
                //\Zx\Test\Test::object_log('$r1', 'false', __FILE__, __LINE__, __CLASS__, __METHOD__);
            }

            $r = $sth->fetch();  //\PDO::FETCH_BOTH is default
            //Note: when result is empty (no record found), it retrun false, but it doesn't meanu query is wrong
            if ($r !== false) {
                //\Zx\Test\Test::object_log('$r', 'not FALSE', __FILE__, __LINE__, __CLASS__, __METHOD__);
            } else {
                $error = $sth->errorInfo();
                if ($error[0] != '00000') { 
                // if 00000 means no error when no result returns (empty result set)
                    \Zx\Test\Test::object_log('$r', $r, __FILE__, __LINE__, __CLASS__, __METHOD__);
                    \Zx\Test\Test::object_log('$r2 FALSE', $sth->errorInfo(), __FILE__, __LINE__, __CLASS__, __METHOD__);
                    \Zx\Test\Test::object_log('$r2 FALSE', $sth->errorCode(), __FILE__, __LINE__, __CLASS__, __METHOD__);
                }
            }
        } catch (PDOException $e) {
            \Zx\Test\Test::object_log('$e->getMessage()', $e->getMessage(), __FILE__, __LINE__, __CLASS__, __METHOD__);
            die('Sorry, something wrong with the site, please try it later!');
        }
        return $r;
    }

    /**
     * 
     * @param array $arr array('title'=>'value', 'description'=>'value',....)
     * @return string
     */
    public static function concat_field_name_and_value($arr) {
        $q = '';
        foreach ($arr as $field_name => $field_value) {
            $q .= "`$field_name`='" . self::$dbh->quote($field_value) . "',";
        }
        $q = substr($q, 0, -1);  //remove last ','
        return $q;
    }

    /**
     * @usage
      $query = Mysql::interpolateQuery($sql, $params);
      \Zx\Test\Test::object_log('query', $query, __FILE__, __LINE__, __CLASS__, __METHOD__);

     * from stackoverflow
      http://stackoverflow.com/questions/210564/pdo-prepared-statements
     * Replaces any parameter placeholders in a query with the value of that
     * parameter. Useful for debugging. Assumes anonymous parameters from 
     * $params are are in the same order as specified in $query
     *
     * @param string $query The sql query with parameter placeholders
     * @param array $params The array of substitution parameters
     * @return string The interpolated query
     */
    public static function interpolateQuery($query, $params) {
        $keys = array();
        $values = $params;

        # build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/' . $key . '/';
            } else {
                $keys[] = '/[?]/';
            }

            if (is_array($value))
                $values[$key] = implode(',', $value);

            if (is_null($value))
                $values[$key] = 'NULL';
        }

        // Walk the array to see if we can add single-quotes to strings, this line might be a problem, we can not add single quote to where clause
        //array_walk($values, create_function('&$v, $k', 'if (!is_numeric($v) && $v!="NULL") $v = "\'".$v."\'";'));

        $query = preg_replace($keys, $values, $query, 1);

        return $query;
    }

}