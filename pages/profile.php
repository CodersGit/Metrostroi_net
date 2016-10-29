<?php
$pl = new User($lnk[1], 'SID');
if ($pl->uid() < 1) {
	include MITRASTROI_ROOT . "pages/404.php";
	exit();
}

Mitrastroi::TakeClass('comments');

$query = $db->execute("SELECT `txtid`, `name` FROM `groups` WHERE NOT `txtid`='ple' ORDER BY `id`");
$groups = array();
$group_names = array();
$steamid = (isset($lnk[1]))? $lnk[1]: "";
$groups_options = '';
$alert = '';
while ($group = $db->fetch_array($query)) {
	array_push($groups, $group['txtid']);
	$groups_options .= "\n\t\t\t<option value=\"{$group['txtid']}\">{$group['name']}</option>";
}
$query = $db->execute("SELECT `tid`, `tname` FROM `tests` ORDER BY `tpriority`");
$tests_options = '';
while ($test = $db->fetch_array($query)) {
	$tests_options .= "\n\t\t\t<option value=\"{$test['tid']}\">{$test['tname']}</option>";
}
$icons_options = "\n\t\t\t<option value=\"0\"><i class=\"fa fa-ban\"></i> Нет иконки</option>";
foreach(Mitrastroi::$ICONS as $id=>$data) {
	$icons_options .= "\n\t\t\t<option value=\"$id\"><i class=\"fa fa-{$data['icon']}\"></i> {$data['name']}</option>";
}

if ($logged_user and isset($lnk[2])/* and $lnk[2] == 'renew'*/ and $logged_user->take_group_info("admin_panel")) {
	$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$_STEAMAPI&steamids=" . Mitrastroi::ToCommunityID($pl->steamid());
	$json_object = file_get_contents($url);
	$json_decoded = json_decode($json_object);

	foreach ($json_decoded->response->players as $player) {
		$status = json_encode(
			array(
				'admin'=>'',
				'nom'=>1,
				'date'=>time()
			)
		);
		$db->execute("INSERT INTO `user_info_cache` (`steamid`, `steam_url`, `avatar_url`, `nickname`) VALUES ('" . $db->safe(Mitrastroi::ToSteamID($player->steamid)) . "', '" . $db->safe($player->profileurl) . "', '" . $db->safe($player->avatarfull) . "', '" . $db->safe($player->personaname) . "')"
			. "ON DUPLICATE KEY UPDATE `steam_url`='" . $db->safe($player->profileurl) . "', `avatar_url`='" . $db->safe($player->avatarfull) . "', `nickname`='" . $db->safe($player->personaname) . "'") or die($db->error());
		header("Location: /profile/" . $pl->steamid());
	}
}
if ($logged_user and (($logged_user->steamid() == $pl->steamid() and !$logged_user->take_mag_info("mag_reason")) or $logged_user->take_group_info("admin_panel")) and isset($_POST['submit']) and $_POST['submit'] == 'profile' and isset($_POST['vk_id']) and isset($_POST['instagram']) and isset($_POST['about']) and isset($_POST['twitter']) and isset($_POST['youtube']) and isset($_POST['twitch'])) {
	$vk = ((int) $_POST['vk_id'])? ("'" . $db->safe((int) $_POST['vk_id']) . "'"): 'NULL';
	$instagram = (strlen($_POST['instagram']) and preg_match("/^[a-zA-Z0-9._-]+$/", $_POST['instagram']))? ("'" . $db->safe($_POST['instagram']) . "'"): 'NULL';
	$twitter = (strlen($_POST['twitter']) and preg_match("/^[a-zA-Z0-9_-]+$/", $_POST['twitter']))? ("'" . $db->safe($_POST['twitter']) . "'"): 'NULL';
	$youtube = (strlen($_POST['youtube']) and preg_match("/^[a-zA-Z0-9_-]+$/", $_POST['youtube']))? ("'" . $db->safe($_POST['youtube']) . "'"): 'NULL';
	$twitch = (strlen($_POST['twitch']) and preg_match("/^[a-zA-Z0-9_-]+$/", $_POST['twitch']))? ("'" . $db->safe($_POST['twitch']) . "'"): 'NULL';
	$about = (strlen($_POST['about']))? ("'" . $db->safe($_POST['about']) . "'"): 'NULL';
	$db->execute("UPDATE `players` SET `vk_id`=$vk, `instagram`=$instagram, `twitter`=$twitter, `youtube`=$youtube, `twitch`=$twitch, `about`=$about WHERE `SID`='{$pl->steamid()}'") or die($db->error());
	$pl = new User($pl->steamid(), 'SID');
}
if ($logged_user and isset($_POST['submit']) and isset($_POST['reason']) and strlen($_POST['reason']))
	switch ($_POST['submit']) {
		case "warn":
			if (!$logged_user->take_group_info("warn"))
				break;
			$db->execute("INSERT INTO `violations` (`SID`, `date`, `admin`, `server`, `violation`)"
				. " VALUES('{$pl->steamid()}', " . time() . ", '{$logged_user->steamid()}', 'Сайт Метростроя', '{$db->safe($_POST['reason'])}')");
			break;
		case 'icon':
			if (!$logged_user->take_group_info("admin_panel") or !isset($_POST['icon']))
				break;
			$db->execute("UPDATE `players` SET `icon`='{$db->safe((int)$_POST['icon'])}' WHERE `id`={$pl->uid()}");
			$pl = new User($pl->steamid(), 'SID');
			break;
		case 'rc':
			if(!((int) $pl->take_coupon_info('nom') > 1 and (int) $pl->take_coupon_info('num') <= 3 and $logged_user->take_group_info("give_coupon")))
				break;
			$status = array(
				'date' => time(),
				'nom' => $pl->take_coupon_info('nom') - 1,
				'admin' => $logged_user->steamid(),
			);
			$db->execute("UPDATE `players` SET `status`='{$db->safe(json_encode($status))}' WHERE `id`={$pl->uid()}");
			$pl = new User($pl->steamid(), 'SID');
			break;
		case 'tc':
			if(!((int) $pl->take_coupon_info('nom') >= 1 and (int) $pl->take_coupon_info('num') <= 3 and $logged_user->take_group_info("give_coupon")))
				break;
			$status = array(
				'date' => time(),
				'nom' => ($pl->take_coupon_info('nom')) % 3 + 1,
				'admin' => $logged_user->steamid(),
			);
			$add = ($pl->take_coupon_info('nom') == 3)? ", `group`='user'": "";
			$db->execute("UPDATE `players` SET `status`='{$db->safe(json_encode($status))}'$add WHERE `id`={$pl->uid()}");
			$vio = "\nОтобран " . Mitrastroi::$COUPON_INFO[$pl->take_coupon_info('nom')] . " талон, выдан "  . Mitrastroi::$COUPON_INFO[($pl->take_coupon_info('nom')) % 3 + 1] . ".";
			$db->execute("INSERT INTO `violations` (`SID`, `date`, `admin`, `server`, `violation`)"
				. " VALUES('{$pl->steamid()}', " . time() . ", '{$logged_user->steamid()}', 'Сайт Метростроя', '{$db->safe($_POST['reason'] . $vio)}')");
			if ($pl->take_coupon_info('nom') == 3) $db->execute("INSERT INTO `examinfo` (`SID`, `date`, `rank`, `examiner`, `note`, `type`, `server`)"
				. " VALUES ('{$pl->steamid()}', " . time() . ", 'user', 'SYSTEM', '{$logged_user->take_steam_info('nickname')}({$logged_user->steamid()}) отобрал красный талон.\n УВОЛЕН!', 2, 'Сайт Метростроя')");
			$pl = new User($pl->steamid(), 'SID');
			break;
		case 'test':
			if (!($logged_user->take_group_info("up_down")))
				break;
			$test = Mitrastroi::GenerateTest($_POST['reason']);
			if ($test) {
				$db->execute("UPDATE `tests_results` SET `status`=3, `reviewer`='BOT', `passed`=0, `review_date`=NOW() WHERE `status` < 2 AND `student`='{$pl->steamid()}'") or die($db->error());
				$db->execute("INSERT INTO `tests_results` (`status`, `student`, `questions`, `trname`, `recived_date`) "
					. "VALUES (0, '{$pl->steamid()}', '{$db->safe($test[0])}', '{$db->safe($test[1])}', NOW())") or die($db->error());
			}
//			$pl = new User($pl->steamid(), 'SID');
			break;
		case 'up':
			if (!($logged_user->take_group_info("up_down") and in_array($pl->take_group_info('txtid'), Mitrastroi::$GROUPS_UP_DOWN) and array_search($pl->take_group_info('txtid'), Mitrastroi::$GROUPS_UP_DOWN) != 4))
				break;
			$new_gr = ($pl->take_group_info('txtid') == 'user' and isset($_POST['up_to_3_class']))? 'driver3class' : Mitrastroi::$GROUPS_UP_DOWN[array_search($pl->take_group_info('txtid'), Mitrastroi::$GROUPS_UP_DOWN) + 1];
			$db->execute("UPDATE `players` SET `group`='$new_gr' WHERE `id`={$pl->uid()}");
			$db->execute("INSERT INTO `examinfo` (`SID`, `date`, `rank`, `examiner`, `note`, `type`, `server`)"
				. " VALUES ('{$pl->steamid()}', " . time() . ", '$new_gr', '{$logged_user->steamid()}', '{$db->safe($_POST['reason'])}', 1, 'Сайт Метростроя')");
			$pl = new User($pl->steamid(), 'SID');
			break;
		case 'down':
			if (!($logged_user->take_group_info("up_down") and in_array($pl->take_group_info('txtid'), Mitrastroi::$GROUPS_UP_DOWN) and array_search($pl->take_group_info('txtid'), Mitrastroi::$GROUPS_UP_DOWN) != 0))
				break;
			$new_gr = Mitrastroi::$GROUPS_UP_DOWN[array_search($pl->take_group_info('txtid'), Mitrastroi::$GROUPS_UP_DOWN) - 1];
			$db->execute("UPDATE `players` SET `group`='$new_gr' WHERE `id`={$pl->uid()}");
			$db->execute("INSERT INTO `examinfo` (`SID`, `date`, `rank`, `examiner`, `note`, `type`, `server`)"
				. " VALUES ('{$pl->steamid()}', " . time() . ", '$new_gr', '{$logged_user->steamid()}', '{$db->safe($_POST['reason'])}', 2, 'Сайт Метростроя')");
			$pl = new User($pl->steamid(), 'SID');
			break;
		case "setrank":
			if (!$logged_user->take_group_info("change_group") or !(isset($_POST['group'])) or !in_array($_POST['group'], $groups))
				break;
			$db->execute("UPDATE `players` SET `group`='{$db->safe($_POST['group'])}' WHERE `id`={$pl->uid()}");
			$db->execute("INSERT INTO `examinfo` (`SID`, `date`, `rank`, `examiner`, `note`, `type`, `server`)"
				. "VALUES ('{$db->safe($pl->steamid())}'," . time() . ",'{$db->safe($_POST['group'])}','{$logged_user->steamid()}','{$db->safe($_POST['reason'])}',4,'Сайт Метростроя')");
			$pl = new User($pl->steamid(), 'SID');
			break;
		case 'report':
			if ($logged_user->steamid() == $pl->steamid() or $logged_user->take_mag_info("mag_reason") or !isset($_POST['server']) or !strlen($_POST['server']))
				break;
			$db->execute("INSERT INTO `mag_reports` (`mag_rserver`,`mag_reason`,`mag_reporter`,`mag_badpl`,`mag_rdate`) VALUES ('{$db->safe($_POST['server'])}', '{$db->safe($_POST['reason'])}', '{$logged_user->steamid()}', '{$pl->steamid()}', '" . time() . "')");
//			$pl = new User($pl->steamid(), 'SID');
			break;
	}
$pl_warns = $db->execute("SELECT * FROM `violations` LEFT JOIN `user_info_cache` ON `violations`.`admin`=`user_info_cache`.`steamid` WHERE  `SID`='{$pl->steamid()}' ORDER BY `violations`.`date` DESC") or die ($db->error());
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
$pl_tests = $db->execute("SELECT * FROM `tests_results` LEFT JOIN `user_info_cache` ON `tests_results`.`reviewer`=`user_info_cache`.`steamid` WHERE `student`='{$pl->steamid()}' AND `status`>1 ORDER BY `tests_results`.`recived_date` DESC") or die ($db->error());
ob_start();
$c = 1;
if (!$db->num_rows($pl_tests)) {
	Mitrastroi::TakeTPL("profile/no_tests");
} else {
	while ($pl_test = $db->fetch_array($pl_tests)) {
		$questions = json_decode($pl_test['questions']);
		$answers = json_decode($pl_test['answers']);
		include Mitrastroi::PathTPL("profile/test");
		$c++;
	}
}
$pl_tests = ob_get_clean();

$pl_exams = $db->execute("SELECT * FROM `groups`, `examinfo` LEFT JOIN `user_info_cache` ON `examinfo`.`examiner`=`user_info_cache`.`steamid` WHERE `examinfo`.`rank`=`groups`.`txtid` AND `SID`='{$pl->steamid()}' ORDER BY `examinfo`.`date` DESC") or die ($db->error());
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