<?php
$amount_by_page = 25;
$page_fucking_title = "Список плохих игроков";
$menu->set_item_active('blacklist');
include Mitrastroi::PathTPL("header");
$page = (!isset($lnk[1]) or $lnk[1] <= 0)? 1: (int) $lnk[1];
include Mitrastroi::PathTPL("left_side");
$first = ($page - 1) * $amount_by_page;
$query = $db->execute("SELECT * FROM `groups`");
while ($gr = $db->fetch_array($query))
	$groups[$gr['txtid']] = $gr['name'];
$query = $db->execute("SELECT *  FROM `blacklist` LEFT JOIN `user_info_cache` ON `user_info_cache`.`steamid`=`blacklist`.`steam_id` LIMIT $first, $amount_by_page") or die($db->error());
Mitrastroi::TakeTPL("bad_players/players_head");
for($c = $first + 1; $typical_ple = $db->fetch_array($query); $c++) {
	include Mitrastroi::PathTPL("bad_players/player_row");
}
Mitrastroi::TakeTPL("bad_players/players_foot");
$query = $db->execute("SELECT COUNT(*) FROM `blacklist`");
$query = $db->fetch_array($query);
echo Mitrastroi::GeneratePagination($page, $amount_by_page, $query[0], "/players/");
include Mitrastroi::PathTPL("right_side");

include Mitrastroi::PathTPL("footer");