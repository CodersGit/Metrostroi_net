<?php
class User {
	private $SID;
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
		$query = $db->execute("SELECT *  FROM `groups`, `players` WHERE `players`.`group`=`groups`.`txtid` AND `$type`='$arg'") or die($db->error());
		if (!$query and $db->num_rows($query) != 1) {
			$this->id = -1;
			return;
		}
		$user = $db->fetch_array($query);
		$this->id = $user['id'];
		$this->SID = $user['SID'];
		$this->up_info = json_decode($user['status']);
		$this->id = $user['id'];
		foreach (Mitrastroi::RIGHTS as $right)
			$this->rights[$right] = $user[$right];
	}
	public function uid() {
		return $this->id;
	}
	public function steamid() {
		return $this->SID;
	}
	public function take_group_info($name) {
		if (!in_array($name, Mitrastroi::RIGHTS))
			return '';
		return $this->rights[$name];
	}
	public function take_up_info($name) {
		if (!isset($this->up_info[$name]))
			return '';
		return $this->up_info[$name];
	}
}