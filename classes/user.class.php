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
		foreach (Mitrastroi::$RIGHTS as $right)
			$this->rights[$right] = $user[$right];
	}

	/**
	 * Returns user's id
	 * @return int
	 */
	public function uid() {
		return $this->id;
	}

	/**
	 * Returns user's SteamID
	 * @return string
	 */
	public function steamid() {
		return $this->SID;
	}

	/**
	 * Returns some info about user's group
	 * @param $name -  Name of parameter
	 * @return string
	 * @throws BadParameterException
	 */
	public function take_group_info($name) {
		if (!in_array($name, Mitrastroi::$RIGHTS))
			throw new BadParameterException();
		return $this->rights[$name];
	}

	/**
	 * Returns some info about latest user's UP
	 * @param $name -  Name of parameter
	 * @return string
	 */
	public function take_up_info($name) {
		if (!isset($this->up_info[$name]))
			return '';
		return $this->up_info[$name];
	}
}
class BadParameterException extends Exception {
	protected $message = 'This function got bad parameter';
}