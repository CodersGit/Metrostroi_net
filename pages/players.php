<?php
$amount_by_page = 25;
$page = (isset($_GET['page']))? 1: (int) $_GET[$page];
$first = ($page - 1) * $amount_by_page;
$query = $db->execute("SELECT * FROM `players` LIMIT $first, $amount_by_page");
Mitrastroi::TakeTPL("players/players_head");
for($c = $first + 1; $typical_ple = $db->fetch_array($query); $c++) {
	include Mitrastroi::PathTPL("players/player_row");
}
Mitrastroi::TakeTPL("players/players_foot");
echo
