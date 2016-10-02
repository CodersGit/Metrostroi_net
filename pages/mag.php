<?php
/*
 *Heey ey it's MAGation day
 *You can blame your dog while we all play today
 *So, Hey, sing it, it's MAGation day
 *I guess it's over now with all your blatant plays
 *Come on
 *Hey ey MAGation Day
 *I got this MAG ban but my friend played today
 *Hey ey MAGation Day
 *See you never again,never again
 */

if (!isset($lnk[1])) $lnk[1] = '';

switch ($lnk[1]) {
	case '':
		$page_fucking_title = "Metrostroi Anti-Grief System (MAG)";
		$menu->set_item_active('MAG');
		include Mitrastroi::PathTPL("header");
		include Mitrastroi::PathTPL("left_side");
		include Mitrastroi::PathTPL("mag/header");
		include Mitrastroi::PathTPL("mag/index");
		include Mitrastroi::PathTPL("right_side");
		include Mitrastroi::PathTPL("footer");
		break;
	case 'list':
		$nbp = 25;
		$page = (isset($lnk[2]) and $lnk[2] > 0)? (int) $lnk[2]: 1;
		$start = $nbp * ($page - 1);
		$query = $db->execute("SELECT `mag_bans`.*, uc.*, (SELECT `nickname` FROM `user_info_cache` WHERE `user_info_cache`.`steamid`=`mag_bans`.`mag_admin_id`) AS `admin_nickname` FROM `mag_bans`,`user_info_cache` uc WHERE `mag_bans`.`mag_steam_id`=uc.`steamid` AND (`mag_bans`.`mag_unban_date` > NOW() OR `mag_bans`.`mag_unban_date` IS NULL) ORDER BY `mag_date` DESC LIMIT $start, $nbp") or die($db->error());
		$menu->set_item_active('MAG_list');
		$page_fucking_title = "MAG-банлист - Страница " . $page;
		include Mitrastroi::PathTPL("header");
		include Mitrastroi::PathTPL("left_side");
		include Mitrastroi::PathTPL("mag/header");
		include Mitrastroi::PathTPL("mag/list_header");
		if ($db->num_rows($query)) {
			while ($ban = $db->fetch_array($query))
				include Mitrastroi::PathTPL("mag/list_item");
		} else include Mitrastroi::PathTPL("mag/list_empty");
		include Mitrastroi::PathTPL("mag/list_footer");
		$query = $db->execute("SELECT COUNT(*) FROM `mag_bans` WHERE `mag_unban_date` > NOW()");
		$query = $db->fetch_array($query);
		echo Mitrastroi::GeneratePagination($page, $nbp, $query[0], "/mag/list/");
		include Mitrastroi::PathTPL("right_side");
		include Mitrastroi::PathTPL("footer");
		break;
	case 'top':
		$nbp = 25;
		$page = (isset($lnk[2]) and $lnk[2] > 0)? (int) $lnk[2]: 1;
		$start = $nbp * ($page - 1);
		$query = $db->execute("SELECT *, (SELECT COUNT(*) FROM `mag_reports` WHERE `mag_reports`.`mag_badpl`=`user_info_cache`.`steamid`) as `warn_num` FROM `user_info_cache` WHERE (SELECT COUNT(*) FROM `mag_reports` WHERE `mag_reports`.`mag_badpl`=`user_info_cache`.`steamid` AND `mag_heavy`>0) > 0 ORDER BY `warn_num` DESC") or die($db->error());
//		$menu->set_item_active('MAG');
		$page_fucking_title = "MAG-топ - Страница " . $page;
		include Mitrastroi::PathTPL("header");
		include Mitrastroi::PathTPL("left_side");
		include Mitrastroi::PathTPL("mag/header");
		include Mitrastroi::PathTPL("mag/top_header");
		if ($db->num_rows($query)) {
			while ($item = $db->fetch_array($query))
				include Mitrastroi::PathTPL("mag/top_item");
		} else include Mitrastroi::PathTPL("mag/top_empty");
		include Mitrastroi::PathTPL("mag/top_footer");
		$query = $db->execute("SELECT COUNT(*) FROM `user_info_cache` WHERE (SELECT COUNT(*) FROM `mag_reports` WHERE `mag_reports`.`mag_badpl`=`user_info_cache`.`steamid` AND `mag_heavy`>0) > 0");
		$query = $db->fetch_array($query);
		echo Mitrastroi::GeneratePagination($page, $nbp, $query[0], "/mag/top/");
		include Mitrastroi::PathTPL("right_side");
		include Mitrastroi::PathTPL("footer");
		break;
	default:
		include MITRASTROI_ROOT . 'pages/404.php';
		break;
}