<?php
if (!isset($lnk[1])) {
	include MITRASTROI_ROOT . "pages/404.php";
	exit();
}
$page = (isset($lnk[2]) and 0 < (int) $lnk[2])? (int) $lnk[2]: 1;
$amount_by_page = 25;
$first = ($page - 1) * $amount_by_page;
$query = $db->execute("SELECT * FROM `groups`, `players` LEFT JOIN `user_info_cache` ON `SID`=`steamid` WHERE `group`=`txtid` AND (`SID`='{$db->safe($lnk[1])}' OR `nickname` LIKE '%{$db->safe($lnk[1])}%') LIMIT $first, $amount_by_page");
$query or die($db->error());
if (!$db->num_rows($query)) {
	header("Location: /player_add/" . $lnk[1]);
	exit();
};
if ($db->num_rows($query) == 1) {
	$query = $db->fetch_array($query);
	header("Location: /profile/" . $query['SID']);
	exit();
}

$page_fucking_title = "Список игроков";
$menu->set_item_active('players');
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");
$page = (!isset($lnk[1]) or $lnk[1] <= 0)? 1: (int) $lnk[1];
$group = $db->execute("SELECT * FROM `groups`");
while ($gr = $db->fetch_array($group))
	$groups[$gr['txtid']] = $gr['name'];
Mitrastroi::TakeTPL("players/players_head");
for($c = $first + 1; $typical_ple = $db->fetch_array($query); $c++) {
	include Mitrastroi::PathTPL("players/player_row");
}
Mitrastroi::TakeTPL("players/players_foot");
$query = $db->execute("SELECT COUNT(*) FROM `players` LEFT JOIN `user_info_cache` ON `SID`=`steamid` WHERE `SID`='{$db->safe($lnk[1])}' OR `nickname` LIKE '{$db->safe($lnk[1])}'");
$query = $db->fetch_array($query);
echo Mitrastroi::GeneratePagination($page, $amount_by_page, $query[0], "/players/");
include Mitrastroi::PathTPL("right_side");

include Mitrastroi::PathTPL("footer");