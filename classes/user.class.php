<?php
class User {
	private $siteamid;
	private $group;
	private $rights;
	private $id;
	private $up_info;

	/**
	 * User constructor.
	 * @param $arg - Some parameter by which we can find only one user
	 * @param string $type - which parameter we send
	 */
	public function User($arg, $type = 'id') {
		global $db;
		$query = $db->execute("SELECT *  FROM `groups`, `players` WHERE `players`.`group`=`groups` AND `$type`='$arg'");
		if (!$query and $db->num_rows($query) != 1) {
			$this->id = -1;
			return;
		}
		$user = $db->fetch_array($query);
		$this->id = $user['id'];
		$this->steamid = $user['SID'];
		$this->up_info = json_decode($user['status']);
		$this->id = $user['id'];
	}
	public function login() {

	}
}