<?php
$amount_by_page = 25;
$page = (isset($lnk[1]))? 1: (int) $lnk[1];
$first = ($page - 1) * $amount_by_page;
$query = $db->execute("SELECT * FROM `groups`");
while ($gr = $db->fetch_array($query))
	$groups[$gr['txtid']] = $gr['name'];
$query = $db->execute("SELECT * FROM `players` LIMIT $first, $amount_by_page");
Mitrastroi::TakeTPL("players/players_head");
for($c = $first + 1; $typical_ple = $db->fetch_array($query); $c++) {
//	include Mitrastroi::PathTPL("players/player_row");
}
Mitrastroi::TakeTPL("players/players_foot");
$query = $db->execute("SELECT COUNT(*) FROM `players`");
$query = $db->fetch_array($query);
echo Mitrastroi::GeneratePagination($page, $amount_by_page, $query[0], "players/");
