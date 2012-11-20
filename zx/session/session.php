<?php
namespace Zx\Session;
defined('SYSTEM_PATH') or die('No direct script access.');
//use SessionHandlerInterface;
use Zx\Model\Mysql;

/**
 * 
CREATE TABLE IF NOT EXISTS `session` (
  `session_id` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '',
  `session_data` text COLLATE utf8_bin NOT NULL,
  `expires` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
 */
class Session implements \SessionHandlerInterface {
    private $old_id;  //for session_regenerate_id()
    public function open($save_path, $session_name) {
        //sessions table must exist
        
        return true;
    }

    public function close() {
        return true;
    }

    /**
     * 
     * @param string $id
     * @return string
     */
    public function read($id) {
        
        $time = time();
        $sql = "SELECT session_data FROM session WHERE session_id=:id AND expires>:time";
        $params = array(':id' => $id, ':time' => $time);
        $session = Mysql::select_one($sql, $params);
        if ($session) {
            $this->old_id = $id;
            return $session['session_data'];
        } else {
            return '';
        }
    }

    /**
     * 
     * @param string $id
     * @param string $data
     * @return bool
     */
    public function write($id, $data) {
        $time = time() + SESSION_LIEFTIME;
        if ($this->old_id) {
            //for session_regenerate_id(), must update original record
            $sql = "UPDATE session SET session_id=:id, session_data=:data, expires=:time 
                WHERE session_id='" . $this->old_id . "'"; 
        } else {
            //others create a new record or replace session data
            $sql = "REPLACE session SET session_id=:id,session_data=:data, expires=:time";
        }
        $params = array(':id' => $id, ':data' => $data, ':time' => $time);
        Mysql::exec($sql, $params);  //update or replace
        return $data;
    }

    /**
     * 
     * @param string $id
     * @return boolean
     */
    public function destroy($id) {
        $sql = "DELETE FROM session WHERE session_id=:id";
        $params = array(':id' => $id);
        Mysql::exec($sql, $params);
        return true;
    }

    /**
     * 
     * @param type $maxlifetime
     * @return boolean
     */
    public function gc($maxlifetime) {
        $sql = "DELETE FROM session WHERE expires< UNIX_TIMESTAMP()";
        Mysql::exec($sql);
        return true;
    }

}


