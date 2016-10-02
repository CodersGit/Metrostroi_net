<?php
if (!$tox1n_lenvaya_jopa or !$tox1n_lenvaya_jopa->take_group_info("mag_reports")) {
	include MITRASTROI_ROOT . "pages/403.php";
	exit();
}

if(!isset($lnk[1])) $lnk[1] = '';

switch ($lnk[1]) {
	case '':
		if (!$tox1n_lenvaya_jopa->take_group_info("mag_reports")) {
			include MITRASTROI_ROOT . "pages/403.php";
			exit();
		}
		$query = $db->execute("SELECT *, (SELECT COUNT(*) FROM `mag_reports` WHERE `mag_badpl`=`steamid` AND `mag_heavy`=0) AS `reports_amount`, (SELECT `nickname` FROM `user_info_cache` WHERE `user_info_cache`.`steamid`=`mag_reports`.`mag_reporter`) AS `reporter` FROM `user_info_cache`, `mag_reports` WHERE `mag_badpl`=`steamid` AND `mag_rid`=(SELECT `mag_rid` FROM `mag_reports` WHERE `mag_badpl`=`steamid` AND `mag_heavy`=0 ORDER BY `mag_rdate` ASC LIMIT 1) ORDER BY (SELECT COUNT(*) FROM `mag_reports` WHERE `mag_badpl`=`steamid` AND `mag_heavy`=0) DESC") or die($db->error());

		$page_fucking_title = "Админка MAG: разбор жалоб";
		$menu->set_item_active('admin_MAG');
		include Mitrastroi::PathTPL("header");
		include Mitrastroi::PathTPL("left_side");
		while ($report = $db->fetch_array($query))
			include Mitrastroi::PathTPL("mag/admin/reports_list_report");
		include Mitrastroi::PathTPL("right_side");
		include Mitrastroi::PathTPL("footer");
		break;
	default:
		if (!$tox1n_lenvaya_jopa->take_group_info("mag_reports")) {
			include MITRASTROI_ROOT . "pages/403.php";
			exit();
		}
		$pl = new User($lnk[1], 'SID');
		if ($pl->uid() < 1) {
			include MITRASTROI_ROOT . "pages/404.php";
			exit();
		}

		if (isset($_POST['mag_submit'])) {
			$reports_before = $pl->count_mag_reports();
			$query = $db->execute("SELECT `mag_rid` FROM `mag_reports`, `user_info_cache` WHERE `mag_badpl`='{$db->safe($lnk[1])}' AND `mag_reporter`=`steamid` AND `mag_heavy`=0");
			while ($rid = $db->fetch_array($query))
				if (isset($_POST['mag_viewed_' . $rid['mag_rid']]))
					$db->execute(
						(isset($_POST['mag_heavy_' . $rid['mag_rid']]))?
							"UPDATE `mag_reports` SET `mag_heavy`=1 WHERE `mag_rid`='{$rid['mag_rid']}'":
							"UPDATE `mag_reports` SET `mag_heavy`=-1 WHERE `mag_rid`='{$rid['mag_rid']}'"
					);
			$pl = new User($lnk[1], 'SID');
			$reports_after = $pl->count_mag_reports();
			if ($reports_after >= 10 and $reports_before < 10)
				$db->execute("INSERT INTO `mag_bans` (`mag_steam_id`,`mag_date`,`mag_reason`) VALUES ('{$pl->steamid()}',NOW(),'Забанен автоматически за 10 нарушений')");
			elseif ($reports_after >= 7 and $reports_before < 7)
				$db->execute("INSERT INTO `mag_bans` (`mag_steam_id`,`mag_date`,`mag_unban_date`,`mag_reason`) VALUES ('{$pl->steamid()}',NOW(),NOW() + INTERVAL 30 DAY,'Забанен автоматически за 7 нарушений')");
			elseif ($reports_after >= 4 and $reports_before < 4)
				$db->execute("INSERT INTO `mag_bans` (`mag_steam_id`,`mag_date`,`mag_unban_date`,`mag_reason`) VALUES ('{$pl->steamid()}',NOW(),NOW() + INTERVAL 7 DAY,'Забанен автоматически за 4 нарушения')");
		}

		$query = $db->execute("SELECT * FROM `mag_reports`, `user_info_cache` WHERE `mag_badpl`='{$db->safe($lnk[1])}' AND `mag_reporter`=`steamid` AND `mag_heavy`=0");

		$page_fucking_title = "Админка MAG: разбор жалоб на игрока " . $pl->take_steam_info('nickname');
		$menu->set_item_active('admin_MAG');
		include Mitrastroi::PathTPL("header");
		include Mitrastroi::PathTPL("left_side");
		include Mitrastroi::PathTPL("tickets/adm_info");
		if ($db->num_rows($query)) {
			include Mitrastroi::PathTPL("mag/admin/reports_rate_header");
			while ($report = $db->fetch_array($query))
				include Mitrastroi::PathTPL("mag/admin/reports_rate_report");
			include Mitrastroi::PathTPL("mag/admin/reports_rate_footer");
		} else include Mitrastroi::PathTPL("mag/admin/reports_rate_none");
		include Mitrastroi::PathTPL("right_side");
		include Mitrastroi::PathTPL("footer");
		break;
}
