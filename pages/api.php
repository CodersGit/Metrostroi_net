<?php
if (!isset($lnk[1])) {
	include MITRASTROI_ROOT . "pages/404.php";
	exit;
}
switch ($lnk[1]) {
	case 'metadmin_version':
		echo Mitrastroi::GetData('metadmin_version');
		break;
	case 'bad':
		exit();
		break;
	case 'icon':
		if (!isset($lnk[2])) {
			include MITRASTROI_ROOT . "pages/404.php";
			exit;
		}
		$query = $db->execute("SELECT * FROM `players` WHERE `SID`='{$db->safe($lnk[2])}'");
		$query or die($db->error());
		if ($db->num_rows($query)) {
			$query = $db->fetch_array($query);
			header('Content-Type: application/json');
			exit($query['icon']);
		}
		break;
	case 'mag_bans':
		$query = $db->execute("SELECT * FROM `mag_bans` WHERE `mag_bans`.`mag_unban_date` > NOW() OR `mag_bans`.`mag_unban_date` IS NULL ORDER BY `mag_bans`.`mag_unban_date` DESC");
		$query or die($db->error());
		$bans = array();
		while ($ban = $db->fetch_array($query)) {
			array_push($bans, array('steamid'=>$ban['mag_steam_id'],'reason'=>$ban['mag_reason'],'unban_date'=>strtotime($ban['mag_unban_date']),));
		}
		header('Content-Type: application/json');
		exit(json_encode($bans));
		break;
	case 'key_check':
		if (!isset($_POST['date']) or !isset($_POST['hash']) or !isset($_POST['port'])) {
			include MITRASTROI_ROOT . "pages/404.php";
			exit;
		}
		$query = $db->execute("SELECT * FROM `servers` WHERE `ip`='" . Mitrastroi::GetRealIp() . "' AND `port`='{$db->safe($_POST['port'])}'");
		$query or die($db->error());
		if ($db->num_rows($query)) {
			$query = $db->fetch_array($query);
			if ($_POST['hash'] != hash("sha256", $_POST['port'] . $_POST['date']. $query['key']))
				exit('bad hash');
			exit("ok");
		} else exit('bad ip or port');
		break;
	case 'report':
		if (!isset($_POST['reason']) or !isset($_POST['date']) or !isset($_POST['target']) or !isset($_POST['author']) or !isset($_POST['hash']) or !isset($_POST['port'])) {
			include MITRASTROI_ROOT . "pages/404.php";
			exit;
		}
		$query = $db->execute("SELECT * FROM `servers` WHERE `ip`='" . Mitrastroi::GetRealIp() . "' AND `port`='{$db->safe($_POST['port'])}'");
		$query or die($db->error());
		if ($db->num_rows($query)) {
			$query = $db->fetch_array($query);
			if ($_POST['hash'] != hash("sha256", $_POST['reason'] . $_POST['date'] . $_POST['target'] . $_POST['author'] . $query['key']))
				exit('bad hash');
			$pl = new User($_POST['author'], 'SID');
			if ($pl->uid() < 1 or $pl->take_mag_info("mag_reason"))
				exit("access denied");
			$db->execute("INSERT INTO `mag_reports` (`mag_rserver`,`mag_reason`,`mag_reporter`,`mag_badpl`,`mag_rdate`) VALUES ('{$db->safe($query['servername'])}', '{$db->safe($_POST['reason'])}', '{$db->safe($_POST['author'])}', '{$db->safe($_POST['target'])}', NOW())");
			exit("ok");
		} else exit('bad ip or port');
		break;
	case 'violation':
		if (!isset($_POST['reason']) or !isset($_POST['date']) or !isset($_POST['target']) or !isset($_POST['author']) or !isset($_POST['hash']) or !isset($_POST['port'])) {
			include MITRASTROI_ROOT . "pages/404.php";
			exit;
		}
		$query = $db->execute("SELECT * FROM `servers` WHERE `ip`='" . Mitrastroi::GetRealIp() . "' AND `port`='{$db->safe($_POST['port'])}'");
		$query or die($db->error());
		if ($db->num_rows($query)) {
			$query = $db->fetch_array($query);
			if ($_POST['hash'] != hash("sha256", $_POST['reason'] . $_POST['date'] . $_POST['target'] . $_POST['author'] . $query['key']))
				exit('bad hash');
			$pl = new User($_POST['author'], 'SID');
			if ($pl->uid() < 1 or !$pl->take_group_info("warn"))
				exit("access denied");
			$db->execute("INSERT INTO `violations` (`server`,`violation`,`admin`,`SID`,`date`) VALUES ('{$db->safe($query['servername'])}', '{$db->safe($_POST['reason'])}', '{$db->safe($_POST['author'])}', '{$db->safe($_POST['target'])}', '" . time() . "')");
			exit("ok");
		} else exit('bad ip or port');
		break;
	case 'set_coupon':
		if (!isset($_POST['number']) or !isset($_POST['date']) or !isset($_POST['target']) or !isset($_POST['author']) or !isset($_POST['hash']) or !isset($_POST['port'])) {
			include MITRASTROI_ROOT . "pages/404.php";
			exit;
		}
		$query = $db->execute("SELECT * FROM `servers` WHERE `ip`='" . Mitrastroi::GetRealIp() . "' AND `port`='{$db->safe($_POST['port'])}'");
		$query or die($db->error());
		if ($db->num_rows($query)) {
			$query = $db->fetch_array($query);
			if ($_POST['hash'] != hash("sha256", $_POST['number'] . $_POST['date'] . $_POST['target'] . $_POST['author'] . $query['key']))
				exit('bad hash');
			$pl = new User($_POST['author'], 'SID');
			if ($pl->uid() < 1 or !$pl->take_group_info("give_coupon"))
				exit("access denied");
			if ($_POST['number'] < 1 or $_POST['number'] >3)
				exit("bad number");
			$status = json_encode(
				array(
					'admin' => $db->safe($_POST['author']),
					'nom' => (int) $db->safe($_POST['number']),
					'date' => $db->safe(time()),
				)
			);
			$db->execute("UPDATE `players` SET `status`='{$db->safe($status)}' WHERE `SID`='{$db->safe($_POST['target'])}'");
			exit("ok");
		} else exit('bad ip or port');
		break;
	case 'setrank':
		if (!isset($_POST['type']) or !isset($_POST['group']) or !isset($_POST['reason']) or !isset($_POST['date']) or !isset($_POST['target']) or !isset($_POST['author']) or !isset($_POST['hash']) or !isset($_POST['port'])) {
			include MITRASTROI_ROOT . "pages/404.php";
			exit;
		}
		$query = $db->execute("SELECT * FROM `servers` WHERE `ip`='" . Mitrastroi::GetRealIp() . "' AND `port`='{$db->safe($_POST['port'])}'");
		$query or die($db->error());
		if ($db->num_rows($query)) {
			$query = $db->fetch_array($query);
			if ($_POST['hash'] != hash("sha256", $_POST['reason'] . $_POST['type'] . $_POST['date'] . $_POST['group'] . $_POST['target'] . $_POST['author'] . $query['key']))
				exit('bad hash');
			$query1 = $db->execute("SELECT `txtid` FROM `groups` WHERE NOT `txtid`='ple' ORDER BY `id`");
			$groups = array();
			while ($group = $db->fetch_array($query1)) {
				array_push($groups, $group['txtid']);
			}
			if (!in_array($_POST['group'], $groups))
				exit("bad group");
			$pl = new User($_POST['author'], 'SID');
			if ($pl->uid() < 1 or !($pl->take_group_info("up_down") or $pl->take_group_info("change_group")))
				exit("access denied");
			$db->execute("UPDATE `players` SET `group`='{$db->safe($_POST['group'])}' WHERE `SID`='{$db->safe($_POST['target'])}'");
			$db->execute("INSERT INTO `examinfo` (`SID`, `date`, `rank`, `examiner`, `note`, `type`, `server`)"
				. "VALUES ('{$db->safe($_POST['target'])}','" . time() . "','{$db->safe($_POST['group'])}','{$db->safe($_POST['author'])}','{$db->safe($_POST['reason'])}','{$db->safe($_POST['type'])}','{$db->safe($query['servername'])}')");
			exit("ok");
		} else exit('bad ip or port');
		break;
	case 'user':
		if (!isset($lnk[2])) {
			include MITRASTROI_ROOT . "pages/404.php";
			exit;
		}
		$pl = new User($lnk[2], 'SID');
		if ($pl->uid() < 1) {
			$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$_STEAMAPI&steamids=" . Mitrastroi::ToCommunityID($lnk[2]);
			$json_object = file_get_contents($url);
			$json_decoded = json_decode($json_object);

			foreach ($json_decoded->response->players as $player) {
				/*echo "
                    <br/>Player ID: $player->steamid
                    <br/>Player Name: $player->personaname
                    <br/>Profile URL: $player->profileurl
                    <br/>SmallAvatar: <img src='$player->avatar'/>
                    <br/>MediumAvatar: <img src='$player->avatarmedium'/>
                    <br/>LargeAvatar: <img src='$player->avatarfull'/>
                    ";*/
				$status = json_encode(
					array(
						'admin'=>'',
						'nom'=>1,
						'date'=>time()
					)
				);
				$db->execute("INSERT INTO `players` (`SID`, `group`, `status`, `session`) VALUES ('" . $db->safe(Mitrastroi::ToSteamID($player->steamid)) . "', 'user', '$status', NULL)");
				$db->execute("INSERT INTO `user_info_cache` (`steamid`, `steam_url`, `avatar_url`, `nickname`) VALUES ('" . $db->safe(Mitrastroi::ToSteamID($player->steamid)) . "', '" . $db->safe($player->profileurl) . "', '" . $db->safe($player->avatarfull) . "', '" . $db->safe($player->personaname) . "')"
					. "ON DUPLICATE KEY UPDATE `steam_url`='" . $db->safe($player->profileurl) . "', `avatar_url`='" . $db->safe($player->avatarfull) . "', `nickname`='" . $db->safe($player->personaname) . "'") or die($db->error());
			}
			$pl = new User($lnk[2], 'SID');
			if ($pl->uid() < 1) {
				include MITRASTROI_ROOT . "pages/404.php";
				exit;
			}
		}
		$pl_warns = $db->execute("SELECT * FROM `violations` LEFT JOIN `user_info_cache` ON `violations`.`admin`=`user_info_cache`.`steamid` WHERE `SID`='{$pl->steamid()}' ORDER BY `violations`.`date` DESC") or die ($db->error());
		$pl_exams = $db->execute("SELECT * FROM `groups`, `examinfo` LEFT JOIN `user_info_cache` ON `examiner`=`user_info_cache`.`steamid` WHERE `examinfo`.`rank`=`groups`.`txtid` AND `SID`='{$pl->steamid()}' ORDER BY `examinfo`.`date` DESC") or die ($db->error());
		$pl_warns_array = array();
		while ($pl_warn = $db->fetch_array($pl_warns)) {
			$pl_warn_array = array(
				'date' => $pl_warn['date'],
				'admin' => $pl_warn['nickname'],
				'server' => $pl_warn['server'],
				'violation' => $pl_warn['violation']
			);
			array_push($pl_warns_array, $pl_warn_array);
		}
		header('Content-Type: application/json');
		$pl_exams_array = array();
		while ($pl_exam = $db->fetch_array($pl_exams)) {
			$pl_exam_array = array(
				'date' => $pl_exam['date'],
				'examiner' => $pl_exam['nickname'],
				'rank' => $pl_exam['rank'],
				'server' => $pl_exam['server'],
				'type' => $pl_exam['type'],
				'note' => $pl_exam['note']
			);
			array_push($pl_exams_array, $pl_exam_array);
		}
		$pl_rights = array();
		foreach(Mitrastroi::$RIGHTS as $RIGHT)
			if ($pl->take_group_info($RIGHT) AND $RIGHT != 'txtid' AND $RIGHT != 'name')
				array_push($pl_rights, $RIGHT);
		$pl_array = array(
			'nick' => $pl->take_steam_info('nickname'),
			'rank' => $pl->take_group_info('txtid'),
			'steamid' => $pl->steamid(),
			'rank_name' => $pl->take_group_info('name'),
			'badpl' => '',
			'mag_banned' => array(
				'reason' => $pl->take_mag_info('mag_reason'),
				'date' => ($pl->take_mag_info('mag_date') != null)? strtotime($pl->take_mag_info('mag_date')): null,
			),
			'status' =>array(
				'nom' => $pl->take_coupon_info('nom'),
				'admin' => $pl->take_coupon_info('admin'),
				'date' => (string) $pl->take_coupon_info('date'),
			),
			'violations' => $pl_warns_array,
			'exam' => $pl_exams_array,
			'icon' => (int) $pl->max_icon_id(),
			'icons' => $pl->icons(),
			'rights' => $pl_rights, //Хелл, твою мать, на большее не рассчитывай
		);
		exit(json_encode($pl_array));
		break;
	case 'search':
		if (!isset($lnk[2])) {
			include MITRASTROI_ROOT . "pages/404.php";
			exit;
		}
		$pls_array = array();
		$query = $db->execute("SELECT `id` FROM `players` LEFT JOIN `user_info_cache` ON `SID`=`steamid` WHERE `SID`='{$db->safe($lnk[2])}' OR `nickname` LIKE '%{$db->safe($lnk[2])}%'");
		header('Content-Type: application/json');
		while ($plid = $db->fetch_array($query)) {
			$pl = new User($plid['id'], 'players`.`id');
			if ($pl->uid() < 1) continue;
			$pl_warns = $db->execute("SELECT * FROM `violations` LEFT JOIN `user_info_cache` ON `violations`.`admin`=`user_info_cache`.`steamid` WHERE `SID`='{$pl->steamid()}' ORDER BY `violations`.`date` DESC") or die ($db->error());
			$pl_exams = $db->execute("SELECT * FROM `groups`, `examinfo` LEFT JOIN `user_info_cache` ON `examiner`=`user_info_cache`.`steamid` WHERE `examinfo`.`rank`=`groups`.`txtid` AND `SID`='{$pl->steamid()}' ORDER BY `examinfo`.`date` DESC") or die ($db->error());
			$pl_warns_array = array();
			while ($pl_warn = $db->fetch_array($pl_warns)) {
				$pl_warn_array = array(
					'date' => $pl_warn['date'],
					'admin' => $pl_warn['nickname'],
					'server' => $pl_warn['server'],
					'violation' => $pl_warn['violation']
				);
				array_push($pl_warns_array, $pl_warn_array);
			}
			$pl_rights = array();
			foreach(Mitrastroi::$RIGHTS as $RIGHT)
				if ($pl->take_group_info($RIGHT) AND $RIGHT != 'txtid' AND $RIGHT != 'name')
					array_push($pl_rights, $RIGHT);
			$pl_exams_array = array();
			while ($pl_exam = $db->fetch_array($pl_exams)) {
				$pl_exam_array = array(
					'date' => $pl_exam['date'],
					'examiner' => $pl_exam['nickname'],
					'rank' => $pl_exam['rank'],
					'server' => $pl_exam['server'],
					'type' => $pl_exam['type'],
					'note' => $pl_exam['note']
				);
				array_push($pl_exams_array, $pl_exam_array);
			}
			$pl_array = array(
				'nick' => $pl->take_steam_info('nickname'),
				'rank' => $pl->take_group_info('txtid'),
				'rank_name' => $pl->take_group_info('name'),
				'steamid' => $pl->steamid(),
				'badpl' => '',
				'mag_banned' => array(
					'reason' => $pl->take_mag_info('mag_reason'),
					'date' => ($pl->take_mag_info('mag_date') != null) ? strtotime($pl->take_mag_info('mag_date')) : null,
				),
				'status' => array(
					'nom' => $pl->take_coupon_info('nom'),
					'admin' => $pl->take_coupon_info('admin'),
					'date' => (string)$pl->take_coupon_info('date'),
				),
				'violations' => $pl_warns_array,
				'exam' => $pl_exams_array,
				'icon' => (int)$pl->max_icon_id(),
				'icons' => $pl->icons(),
				'rights' => $pl_rights, //Хелл, твою мать, на большее не рассчитывай
			);
			array_push($pls_array, $pl_array);
		}
		exit(json_encode($pls_array));
		break;
	case 'groups':
		$query = $db->execute("SELECT * FROM `groups`");
		$query or die($db->error());
		$groups_array = array();
		while ($group = $db->fetch_array($query)) {
			$group_array = array(
				$group['txtid'] => $group['name']
			);
			array_push($groups_array, $group_array);
		}
		header('Content-Type: application/json');
		exit(json_encode($groups_array));
		break;
	case 'icons':
		header('Content-Type: application/json');
		exit(json_encode(Mitrastroi::$ICONS));
		break;
	case 'icon_view':
		if (!isset($lnk[2]) or !isset(Mitrastroi::$ICONS[(int)$lnk[2]])) {
			include MITRASTROI_ROOT . "pages/404.php";
			exit;
		}
		include Mitrastroi::PathTPL('api_icon');
		exit;
		break;
	default:
		include MITRASTROI_ROOT . "pages/404.php";
		exit;
		break;
}