<?php
/**
 * ANIX Session handling class
 *
 * Created on 12.03.2008
 * @license    http://www.opensource.org/licenses/cpl.php Common Public License 1.0
 */

class Session
{

	/**
     * Open the session
     * @return bool
     */
	public static function open() {
		return true;
	}

	/**
     * Close the session
     * @return bool
     */
	public static function close() {
		return true;
	}

	/**
     * Read the session
     * @param int session id
     * @return string string of the sessoin
     */
	public static function read($id) {
		$dbLink = dbConnect();
		$id = mysql_real_escape_string($id);
		$sql = sprintf("SELECT `session_data` FROM `gen_session` " .
		"WHERE `session` = '%s'", $id);
		$result = request($sql,$dbLink);

		if (mysql_num_rows($result)) {
			$record = mysql_fetch_assoc($result);
			mysql_close($dbLink);
			return $record['session_data'];
		}
		mysql_close($dbLink);
		return '';
	}

	/**
     * Write the session
     * @param int session id
     * @param string data of the session
     */
	public static function write($id, $data) {
		$dbLink = dbConnect();
		$sql = sprintf("REPLACE INTO `gen_session` VALUES('%s', '%s', '%s')",
			mysql_real_escape_string($id),
			mysql_real_escape_string(time()),
			mysql_real_escape_string($data)
		);
		request($sql, $dbLink);
		mysql_close($dbLink);
		return true;
	}

	/**
     * Destoroy the session
     * @param int session id
     * @return bool
     */
	public static function destroy($id) {
		$dbLink = dbConnect();
		$sql = sprintf("DELETE FROM `gen_session` WHERE `session` = '%s'", $id);
		request($sql,$dbLink);
		mysql_close($dbLink);
		return true;
	}

	/**
     * Garbage Collector
     * @param int life time (sec.)
     * @return bool
     * @see session.gc_divisor      100
     * @see session.gc_maxlifetime 1440
     * @see session.gc_probability    1
     * @usage execution rate 1/100
     *        (session.gc_probability/session.gc_divisor)
     */
	public static function gc($max) {
		$dbLink = dbConnect();
		$sql = sprintf("DELETE FROM `sessions` WHERE `session_expires` < '%s'",
			mysql_real_escape_string(time() - $max));
		request($sql,$dbLink);
		mysql_close($dbLink);
		return true;
	}
}