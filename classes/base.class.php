<?php
class Mitrastroi {
	public static $RIGHTS = array(
		'txtid', 'name', 'change_group', 'warn', 'news_add', 'delete_comment', 'blacklist_edit', 'tests_edit', 'give_coupon', 'up_down', 'admin_panel', 'tickets', 'mag_bans', 'edit_tests', 'mag_reports'
	);
	public static $STEAM_INFO = array(
		'steamid', 'nickname', 'steam_url', 'avatar_url'
	);
	public static $BAN_INFO = array(
		'steam_id', 'admin', 'reason'
	);
	public static $SOCIAL_INFO = array(
		'vk_id', 'about', 'instagram', 'twitter', 'youtube', 'twitch', 'site'
	);
	public static $MAG_INFO = array(
		'mag_steam_id', 'mag_admin_id', 'mag_date', 'mag_unban_date', 'mag_reason'
	);
	public static $COUPON_INFO = array(
		'розовый', 'зеленый', 'желтый', 'красный'
	);
	public static $GROUPS_UP_DOWN = array(
		'user', 'driver', 'driver3class', 'driver2class', 'driver1class'
	);
	public static $ICONS = array(
		-1 => array(
			'name'=>'Недоверенный игрок',
			'color'=>'danger',
			'icon'=>'ban',
		),
		1 => array(
			'name'=>'Заслуженный игрок',
			'color'=>'warning',
			'icon'=>'star-o',
		),
		2 => array(
			'name'=>'Доверенный игрок',
			'color'=>'success',
			'icon'=>'star',
		),
		3 => array(
			'name'=>'Владелец партнерского сервера',
			'color'=>'primary',
			'icon'=>'server',
		),
		6 => array(
			'name'=>'Владелец партнерских серверов',
			'color'=>'info',
			'icon'=>'sitemap',
		),
		9 => array(
			'name'=>'Пресс-служба',
			'color'=>'warning',
			'icon'=>'pencil',
		),
		10 => array(
			'name'=>'Модератор системы',
			'color'=>'danger',
			'icon'=>'comments-o',
		),
		11 => array(
			'name'=>'Разработчик системы',
			'color'=>'primary',
			'icon'=>'wrench',
		),
		12 => array(
			'name'=>'Разработчик мода',
			'color'=>'success',
			'icon'=>'subway',
		),
	);
	private static $DATA;
	public static function ToCommunityID($id) {
		if (preg_match('/^STEAM_/', $id)) {
			$parts = explode(':', $id);
			return bcadd(bcadd(bcmul($parts[2], '2'), '76561197960265728'), $parts[1]);
		} elseif (is_numeric($id) && strlen($id) < 16) {
			return bcadd($id, '76561197960265728');
		} else {
			return $id; // We have no idea what this is, so just return it.
		}
	}

	public static function GetRealIp(){
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else
			$ip = $_SERVER['REMOTE_ADDR'];
		return substr($ip, 0, 16);
	}

	public static function ToSteamID($id) {
		if (is_numeric($id) && strlen($id) >= 16) {
			$z = bcdiv(bcsub($id, '76561197960265728'), '2');
		} elseif (is_numeric($id)) {
			$z = bcdiv($id, '2'); // Actually new User ID format
		} else {
			return $id; // We have no idea what this is, so just return it.
		}
		$y = bcmod($id, '2');
		return 'STEAM_0:' . $y . ':' . floor($z);
	}

	public static function randString($pass_len = 50) {
		$allchars = "ABCDEFGHIJKLMNOPQRSYUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		$string = "";

		mt_srand((double)microtime() * 1000000);

		for ($i = 0; $i < $pass_len; $i++)
			$string .= $allchars{mt_rand(0, strlen($allchars) - 1)};

		return $string;
	}

	public static function GenerateTest ($id) {
		global $db;
		$test = $db->execute("SELECT * FROM `tests` WHERE `tid`='{$db->safe($id)}'");
		if ($db->num_rows($test) != 1)
			return false;
		$test = $db->fetch_array($test);
		$tname = $test['tname'];
		$test = json_decode($test['questions_cats']);
		$mscats = $db->execute("SELECT * FROM `questions_cats`");
		$cats = array();
		while ($cat = $db->fetch_array($mscats))
			array_push($cats, $cat['qcid']);
		$result = array();
		$where = array();
		foreach ($test as $question)
			$where[$question] = '';
		foreach ($test as $question) {
			$tmpquestion = $db->execute(
				(in_array($question, $cats))?
					"SELECT * FROM `questions` WHERE `cat`='{$db->safe($question)}'{$where[$question]} ORDER BY RAND()":
					"SELECT * FROM `questions` WHERE `cat`='0'{$where[$question]} ORDER BY RAND()"
			);
			if (!$db->num_rows($tmpquestion))
				continue;
			$tmpquestion = $db->fetch_array($tmpquestion);
			$where[$question] .= " AND NOT (`qid`='{$tmpquestion['qid']}')";
			array_push($result, $tmpquestion['question']);
		}
		return array (json_encode($result), $tname);
	}

	public static function TakeAuth() {
		global $logged_user, $db;
		if(!isset($_COOKIE['mitrastroi_sid'])) {
			$logged_user = false;
			return;
		}
		$db->execute("DELETE FROM `sessions` WHERE `session_date` < NOW() - INTERVAL 1 MONTH ");
		$user = new User($_COOKIE['mitrastroi_sid'], 'session_id');
		if($user->uid() <= 0) {
			$logged_user = false;
			return;
		}
		$logged_user = $user;
		$sessionID = Mitrastroi::randString(128);
		$db->execute("UPDATE `sessions` SET `session_id`='$sessionID', `session_date`=NOW() WHERE `session_id`='{$db->safe($_COOKIE['mitrastroi_sid'])}'");
		setcookie("mitrastroi_sid", $sessionID, time() + 3600 * 24 * 30, '/');
		$_COOKIE['mitrastroi_sid'] = $sessionID;
	}

	public static function SetData($key, $value) {
		global $db;
		$query = $db->execute("INSERT INTO `data` (`key`,`value`) VALUES ('{$db->safe($key)}','{$db->safe($value)}')"
			. "ON DUPLICATE KEY UPDATE `value`='{$db->safe($value)}'");
		return self::$DATA[$key] = ($query)? $value:false;
	}

	public static function GetData($key) {
		global $db;
		if (isset(self::$DATA[$key]))
			return self::$DATA[$key];
		$query = $db->execute("SELECT `value` FROM `data` WHERE `key`='{$db->safe($key)}'");
		$value = $db->fetch_array($query);
		return self::$DATA[$key] = ($db->num_rows($query))? $value['value']:false;
	}

	public static function TakeClass ($class) {
		if (file_exists(MITRASTROI_ROOT . "classes/$class.class.php")) {
			require_once (MITRASTROI_ROOT . "classes/$class.class.php");
			return true;
		}
		return false;
	}

	public static function TakeTPL ($tpl) {
		if (file_exists(MITRASTROI_ROOT . "tpl/$tpl.html")) {
			include(MITRASTROI_ROOT . "tpl/$tpl.html");
			return true;
		}
		return false;
	}

	public static function PathTPL ($tpl) {
		if (file_exists(MITRASTROI_ROOT . "tpl/$tpl.html")) {
			return (MITRASTROI_ROOT . "tpl/$tpl.html");
		}
		return false;
	}

	public static function GeneratePagination($page, $amount_by_page, $total_amount, $link) {
		if ($amount_by_page >= $total_amount) return '';
		ob_start();
		self::TakeTPL("pagination/pagin_start");
		$pages_count = (int) ($total_amount / $amount_by_page + 1);
		if ($page <= 5){
			for ($p = 1; $p < $page; $p++){
				$l = $link . $p;
				include Mitrastroi::PathTPL("pagination/pagin_item_inactive");
			}
		} else {
			$p = "&laquo;";
			$l = $link . 1;
			include Mitrastroi::PathTPL("pagination/pagin_item_inactive");
			for ($p = $page - 3; $p < $page; $p++){
				$l = $link . $p;
				include Mitrastroi::PathTPL("pagination/pagin_item_inactive");
			}
		}
		$p = $page;
		$l = $link . $page;
		include Mitrastroi::PathTPL("pagination/pagin_item_active");
		if ($pages_count - $page <= 5){
			for ($p = $page + 1; $p <= $pages_count; $p++){
				$l = $link . $p;
				include Mitrastroi::PathTPL("pagination/pagin_item_inactive");
			}
		} else {
			for ($p = $page + 1; $p <= $page + 3; $p++){
				$l = $link . $p;
				include Mitrastroi::PathTPL("pagination/pagin_item_inactive");
			}
			$p = "&raquo;";
			$l = $link . $pages_count;
			include Mitrastroi::PathTPL("pagination/pagin_item_inactive");
		}
		self::TakeTPL("pagination/pagin_end");
		return ob_get_clean();
	}
}