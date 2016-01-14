<?php
$pl = new User($lnk[1], 'SID');
if ($pl->uid() < 1) {
	include MITRASTROI_ROOT . "pages/404.php";
	exit();
}

//TODO ЧС

$query = $db->execute("SELECT `txtid`, `name` FROM `groups` WHERE NOT `txtid`='ple' ORDER BY `id`");
$groups = array();
$steamid = (isset($lnk[1]))? $lnk[1]: "";
$groups_options = '';
$alert = '';
while ($group = $db->fetch_array($query)) {
	array_push($groups, $group['txtid']);
	$groups_options .= "\n\t\t\t<option value=\"{$group['txtid']}\">{$group['name']}</option>";
}
if ($tox1n_lenvaya_jopa and isset($_POST['submit']) and isset($_POST['reason']) and strlen($_POST['reason']))
	switch ($_POST['submit']) {
		case "warn":
			if (!$tox1n_lenvaya_jopa->take_group_info("warn"))
				break;
			$db->execute("INSERT INTO `violations` (`SID`, `date`, `admin`, `server`, `violation`)"
				. " VALUES('{$pl->steamid()}', " . time() . ", '{$tox1n_lenvaya_jopa->steamid()}', 'Сайт Метростроя', '{$db->safe($_POST['reason'])}')");
			break;
		case 'rc':
			if(!((int) $pl->take_coupon_info('nom') > 1 and (int) $pl->take_coupon_info('num') <= 3 and $tox1n_lenvaya_jopa->take_group_info("give_coupon")))
				break;
			$status = array(
				'date' => time(),
				'nom' => $pl->take_coupon_info('nom') - 1,
				'admin' => $tox1n_lenvaya_jopa->steamid(),
			);
			$db->execute("UPDATE `players` SET `status`='{$db->safe(json_encode($status))}' WHERE `id`={$pl->uid()}");
			$pl = new User($pl->steamid(), 'SID');
			break;
		case 'tc':
			if(!((int) $pl->take_coupon_info('nom') >= 1 and (int) $pl->take_coupon_info('num') <= 3 and $tox1n_lenvaya_jopa->take_group_info("give_coupon")))
				break;
			$status = array(
				'date' => time(),
				'nom' => ($pl->take_coupon_info('nom')) % 3 + 1,
				'admin' => $tox1n_lenvaya_jopa->steamid(),
			);
			$add = ($pl->take_coupon_info('nom') == 3)? ", `group`='user'": "";
			$db->execute("UPDATE `players` SET `status`='{$db->safe(json_encode($status))}'$add WHERE `id`={$pl->uid()}");
			$vio = "\nОтобран " . Mitrastroi::$COUPON_INFO[$pl->take_coupon_info('nom')] . " талон, выдан "  . Mitrastroi::$COUPON_INFO[($pl->take_coupon_info('nom')) % 3 + 1] . ".";
			$db->execute("INSERT INTO `violations` (`SID`, `date`, `admin`, `server`, `violation`)"
				. " VALUES('{$pl->steamid()}', " . time() . ", '{$tox1n_lenvaya_jopa->steamid()}', 'Сайт Метростроя', '{$db->safe($_POST['reason'] . $vio)}')");
			if ($pl->take_coupon_info('nom') == 3) $db->execute("INSERT INTO `examinfo` (`SID`, `date`, `rank`, `examiner`, `note`, `type`, `server`)"
				. " VALUES ('{$pl->steamid()}', " . time() . ", 'user', 'SYSTEM', '{$tox1n_lenvaya_jopa->take_steam_info('nickname')}({$tox1n_lenvaya_jopa->steamid()}) отобрал красный талон.\n УВОЛЕН!', 2, 'Сайт Метростроя')");
			$pl = new User($pl->steamid(), 'SID');
			break;
		case 'up':
			if (!($tox1n_lenvaya_jopa->take_group_info("up_down") and in_array($pl->take_group_info('txtid'), Mitrastroi::$GROUPS_UP_DOWN) and array_search($pl->take_group_info('txtid'), Mitrastroi::$GROUPS_UP_DOWN) != 3))
				break;
			$new_gr = Mitrastroi::$GROUPS_UP_DOWN[array_search($pl->take_group_info('txtid'), Mitrastroi::$GROUPS_UP_DOWN) + 1];
			$db->execute("UPDATE `players` SET `group`='$new_gr' WHERE `id`={$pl->uid()}");
			$db->execute("INSERT INTO `examinfo` (`SID`, `date`, `rank`, `examiner`, `note`, `type`, `server`)"
				. " VALUES ('{$pl->steamid()}', " . time() . ", '$new_gr', '{$tox1n_lenvaya_jopa->steamid()}', '{$db->safe($_POST['reason'])}', 1, 'Сайт Метростроя')");
			$pl = new User($pl->steamid(), 'SID');
			break;
		case 'down':
			if (!($tox1n_lenvaya_jopa->take_group_info("up_down") and in_array($pl->take_group_info('txtid'), Mitrastroi::$GROUPS_UP_DOWN) and array_search($pl->take_group_info('txtid'), Mitrastroi::$GROUPS_UP_DOWN) != 0))
				break;
			$new_gr = Mitrastroi::$GROUPS_UP_DOWN[array_search($pl->take_group_info('txtid'), Mitrastroi::$GROUPS_UP_DOWN) - 1];
			$db->execute("UPDATE `players` SET `group`='$new_gr' WHERE `id`={$pl->uid()}");
			$db->execute("INSERT INTO `examinfo` (`SID`, `date`, `rank`, `examiner`, `note`, `type`, `server`)"
				. " VALUES ('{$pl->steamid()}', " . time() . ", '$new_gr', '{$tox1n_lenvaya_jopa->steamid()}', '{$db->safe($_POST['reason'])}', 2, 'Сайт Метростроя')");
			$pl = new User($pl->steamid(), 'SID');
			break;
		case "setrank":
			if (!$tox1n_lenvaya_jopa->take_group_info("change_group") or !(isset($_POST['group'])) or !in_array($_POST['group'], $groups))
				break;
			$db->execute("UPDATE `players` SET `group`='{$db->safe($_POST['group'])}' WHERE `id`={$pl->uid()}");
			$db->execute("INSERT INTO `examinfo` (`SID`, `date`, `rank`, `examiner`, `note`, `type`, `server`)"
				. "VALUES ('{$db->safe($pl->steamid())}'," . time() . ",'{$db->safe($_POST['group'])}','{$tox1n_lenvaya_jopa->steamid()}','{$db->safe($_POST['reason'])}',4,'Сайт Метростроя')");
			$pl = new User($pl->steamid(), 'SID');
			break;
	}
$pl_warns = $db->execute("SELECT * FROM `violations` LEFT JOIN `user_info_cache` ON `violations`.`admin`=`user_info_cache`.`steamid` WHERE  `SID`='{$pl->steamid()}'") or die ($db->error());
ob_start();
$c = 1;
if (!$db->num_rows($pl_warns)) {
	Mitrastroi::TakeTPL("profile/no_violations");
} else {
	while ($pl_warn = $db->fetch_array($pl_warns)) {
		include Mitrastroi::PathTPL("profile/violation");
		$c++;
	}
}
$pl_warns = ob_get_clean();

$pl_exams = $db->execute("SELECT * FROM `groups`, `examinfo` LEFT JOIN `user_info_cache` ON `examinfo`.`examiner`=`user_info_cache`.`steamid` WHERE `examinfo`.`rank`=`groups`.`txtid` AND `SID`='{$pl->steamid()}'") or die ($db->error());
ob_start();
if (!$db->num_rows($pl_exams)) {
	Mitrastroi::TakeTPL("profile/no_exams");
} else {
	while ($pl_exam = $db->fetch_array($pl_exams)) {
		switch ($pl_exam['type']) {
			case 1:
				$class = 'success';
				break;
			case 2:
				$class = 'danger';
				break;
			case 3:
				$class = 'warning';
				break;
			case 4:
				$class = 'info';
				break;
			default:
				$class = 'default';
				break;
		}
		include Mitrastroi::PathTPL("profile/exam");
	}
}
$pl_exams = ob_get_clean();

$page_fucking_title = "Профиль пользователя - " . $pl->take_steam_info("nickname");
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");

include Mitrastroi::PathTPL("profile/profile");

include Mitrastroi::PathTPL("right_side");
include Mitrastroi::PathTPL("footer");