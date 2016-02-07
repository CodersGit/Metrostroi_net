<?php
class User {
	private $SID;
	private $rights;
	private $id;
	private $coupon_info;
	private $steam_info;
	private $ban;
	private $icon;
	private $violations;

	/**
	 * User constructor.
	 * @param $arg - Some parameter by which we can find only one user
	 * @param string $type - which parameter we send
	 */
	public function User($arg, $type = 'id') {
		global $db;
		$query = $db->execute("SELECT *  FROM `groups`, `players` LEFT JOIN `user_info_cache` ON `user_info_cache`.`steamid`=`players`.`SID` LEFT JOIN `blacklist` ON `blacklist`.`steam_id`=`players`.`SID` WHERE `players`.`group`=`groups`.`txtid` AND `$type`='$arg'") or die($db->error());
		if (!$query and $db->num_rows($query) != 1) {
			print $db->error();
			$this->id = -1;
			return;
		}
		$user = $db->fetch_array($query);
		$this->id = $user['id'];
		$this->SID = $user['SID'];
		$this->icon = (int) $user['icon'];
		$this->coupon_info = json_decode($user['status']);
		$this->id = $user['id'];
		foreach (Mitrastroi::$RIGHTS as $right)
			$this->rights[$right] = $user[$right];
		foreach (Mitrastroi::$BAN_INFO as $ban)
			$this->ban[$ban] = $user[$ban];
		foreach (Mitrastroi::$STEAM_INFO as $INFO)
			$this->steam_info[$INFO] = $user[$INFO];
	}

	/**
	 * Returns user's id
	 * @return int
	 */
	public function uid() {
		return $this->id;
	}

	/**
	 * Shows icon
	 * @param $id - ID of icon
	 * @return string
	 */
	public static function ShowIcon($id) {
		if (!isset(Mitrastroi::$ICONS[$id])) return '';
		return "<div class=\"label label-" . Mitrastroi::$ICONS[$id]['color'] . " stt\" data-toggle=\"tooltip\" data-placement=\"top\" title=\"" . Mitrastroi::$ICONS[$id]['name'] . "\"><i class=\"fa fa-" . Mitrastroi::$ICONS[$id]['icon'] . "\"></i></div>";
	}

	/**
	 * Shows full user's icon
	 * @return string
	 */
	public function show_full_icon() {
		if (!isset(Mitrastroi::$ICONS[$this->icon])) return '';
		return "<div class=\"label label-" . Mitrastroi::$ICONS[$this->icon]['color'] . "\"><i class=\"fa fa-" . Mitrastroi::$ICONS[$this->icon]['icon'] . "\"></i> " . Mitrastroi::$ICONS[$this->icon]['name'] . "</div>";
	}

	/**
	 * Shows user's icon
	 * @return string
	 */
	public function show_icon() {
		return self::ShowIcon($this->icon);
	}

	/**
	 * Returns user's SteamID
	 * @return string
	 */
	public function steamid() {
		return $this->SID;
	}

	/**
	 * Returns user's icon ID
	 * @return int
	 */
	public function icon_id() {
		return $this->icon;
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
	 * Returns some info about user's Steam account
	 * @param $name -  Name of parameter
	 * @return string
	 * @throws BadParameterException
	 */
	public function take_steam_info($name) {
		if (!in_array($name, Mitrastroi::$STEAM_INFO))
			throw new BadParameterException();
		return $this->steam_info[$name];
	}

	/**
	 * Returns some info about user's coupon
	 * @param $name -  Name of parameter
	 * @return string
	 */
	public function take_coupon_info($name) {
		if (!isset($this->coupon_info->$name))
			return '';
		return $this->coupon_info->$name;
	}

	/**
	 * Returns number of violations
	 * @return string
	 */
	public function count_violations() {
		global $db;
		if (isset($this->violations)) return $this->violations;
		$query = $db->execute("SELECT COUNT(*) FROM `violations` WHERE `SID`='{$this->SID}'");
		$query = $db->fetch_array($query);
		$this->violations = $query[0];
		return $query[0];
	}

	/**
	 * Returns some info about user's ban
	 * @param $name -  Name of parameter
	 * @return string
	 */
	public function take_ban_info($name) {
		if (!in_array($name, Mitrastroi::$BAN_INFO))
			return '';
		return ($this->ban[$name] != null)? $this->ban[$name]: false;
	}

	/**
	 * User logout
	 */
	public function logout() {
		global $db;
		$db->execute("UPDATE `accounts` SET `session`=NULL WHERE `id`={$this->id}");
		setcookie("mitrastroi_sid", 'null', time(), '/');
	}
}
class BadParameterException extends Exception {
	protected $message = 'This function got bad parameter';
}