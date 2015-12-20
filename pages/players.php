<?php
$amount_by_page = 25;
$page_fucking_title = "Список игроков";
$menu->set_item_active('players');
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");
$page = (!isset($lnk[1]) or $lnk[1] <= 0)? 1: (int) $lnk[1];
$first = ($page - 1) * $amount_by_page;
$query = $db->execute("SELECT * FROM `groups`");
while ($gr = $db->fetch_array($query))
	$groups[$gr['txtid']] = $gr['name'];
$query = $db->execute("SELECT *  FROM `groups`, `players` LEFT JOIN `user_info_cache` ON `user_info_cache`.`steamid`=`players`.`SID` WHERE `players`.`group`=`groups`.`txtid` LIMIT $first, $amount_by_page") or die($db->error());
Mitrastroi::TakeTPL("players/players_head");
for($c = $first + 1; $typical_ple = $db->fetch_array($query); $c++) {
	include Mitrastroi::PathTPL("players/player_row");
}
Mitrastroi::TakeTPL("players/players_foot");
$query = $db->execute("SELECT COUNT(*) FROM `players`");
$query = $db->fetch_array($query);
echo Mitrastroi::GeneratePagination($page, $amount_by_page, $query[0], "/players/");
include Mitrastroi::PathTPL("right_side");

include Mitrastroi::PathTPL("footer");