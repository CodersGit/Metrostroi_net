<?php
$pl = new User($lnk[1], 'SID');
if ($pl->uid() < 1) {
	include MITRASTROI_ROOT . "pages/404.php";
	exit();
}

//TODO действия с пользователями

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

$pl_exams = $db->execute("SELECT * FROM `examinfo`, `groups`, `user_info_cache` WHERE `examinfo`.`rank`=`groups`.`txtid` AND `examinfo`.`examiner`=`user_info_cache`.`steamid` AND `SID`='{$pl->steamid()}'") or die ($db->error());
ob_start();
if (!$db->num_rows($pl_exams)) {
	Mitrastroi::TakeTPL("profile/no_exams");
} else {
	while ($pl_exam = $db->fetch_array($pl_exams)) {
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