<?php
if (!isset($lnk[1])) {
	include MITRASTROI_ROOT . "pages/404.php";
	exit;
}
switch ($lnk[1]) {
	case 'bad':
		if (!isset($lnk[2])) {
			include MITRASTROI_ROOT . "pages/404.php";
			exit;
		}
		$query = $db->execute("SELECT `reason` FROM `blacklist` WHERE `steam_id`='{$db->safe($lnk[2])}'");
		$query or die($db->error());
		if ($db->num_rows($query)) {
			$query = $db->fetch_array($query);
			exit($query['reason']);
		}
		break;
	case 'user':
		if (!isset($lnk[2])) {
			include MITRASTROI_ROOT . "pages/404.php";
			exit;
		}
		$pl = new User($lnk[2], 'SID');
		if ($pl->uid() < 1) {
			exit;
		}
		$pl_warns = $db->execute("SELECT * FROM `violations` LEFT JOIN `user_info_cache` ON `violations`.`admin`=`user_info_cache`.`steamid` WHERE `SID`='{$pl->steamid()}' ORDER BY `violations`.`date` DESC") or die ($db->error());
		$pl_exams = $db->execute("SELECT * FROM `groups`, `examinfo` LEFT JOIN `user_info_cache` ON `examiner`=`user_info_cache`.`steamid` WHERE `examinfo`.`rank`=`groups`.`txtid` AND `SID`='{$pl->steamid()}' ORDER BY `examinfo`.`date` DESC") or die ($db->error());
		$pl_warns_array = array();
		while ($pl_warn = $db->fetch_array($pl_warns)) {
			$pl_warn_array = array(
				'date' => $pl_warn['date'],
				'admin' => $pl_warn['admin'],
				'server' => $pl_warn['server'],
				'violation' => $pl_warn['violation']
			);
			array_push($pl_warns_array, $pl_warn_array);
		}
		$pl_exams_array = array();
		while ($pl_exam = $db->fetch_array($pl_exams)) {
			$pl_exam_array = array(
				'date' => $pl_exam['date'],
				'examiner' => $pl_exam['examiner'],
				'rank' => $pl_exam['rank'],
				'server' => $pl_exam['server'],
				'type' => $pl_exam['type'],
				'note' => $pl_exam['note']
			);
			array_push($pl_exams_array, $pl_exam_array);
		}
		$pl_array = array(
			'Nick' => $pl->take_steam_info('nickname'),
			'rank' => $pl->take_group_info('txtid'),
			'badpl' => ($pl->take_ban_info('reason'))? ($pl->take_ban_info('reason') . '|' . $pl->take_ban_info('admin')): '',
			'status' =>array(
				'nom' => $pl->take_coupon_info('nom'),
				'admin' => $pl->take_coupon_info('admin'),
				'date' => (string) $pl->take_coupon_info('date'),
			),
			'violations' => $pl_warns_array,
			'exam' => $pl_exams_array,
		);
		exit(json_encode($pl_array));
		break;
	default:
		include MITRASTROI_ROOT . "pages/404.php";
		exit;
		break;
}