<?php
if (!$logged_user or $logged_user->max_icon_id() < 6) {
	include MITRASTROI_ROOT . "pages/403.php";
	exit();
}
$alert = '';
$owner = (isset($_POST['owner']) and $logged_user->take_group_info("admin_panel")) ? $_POST['owner'] : $logged_user->steamid();

if (isset($_POST['name']) and isset($_POST['ip']) and isset($_POST['port'])) {
	$active = (isset($_POST['active']))? 1:0;

	$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$_STEAMAPI&steamids=" . Mitrastroi::ToCommunityID($owner);
	$json_object = file_get_contents($url);
	if (!$json_object or !strlen($json_object)) die ("Error");
	$json_decoded = json_decode($json_object);
	$is = false;
	foreach ($json_decoded->response->players as $player) {
		$is = true;
		$db->execute("INSERT INTO `user_info_cache` (`steamid`, `steam_url`, `avatar_url`, `nickname`) VALUES ('" . $db->safe(Mitrastroi::ToSteamID($player->steamid)) . "', '" . $db->safe($player->profileurl) . "', '" . $db->safe($player->avatarfull) . "', '" . $db->safe($player->personaname) . "')"
			. "ON DUPLICATE KEY UPDATE `steam_url`='" . $db->safe($player->profileurl) . "', `avatar_url`='" . $db->safe($player->avatarfull) . "', `nickname`='" . $db->safe($player->personaname) . "'") or die($db->error());
		$db->execute("INSERT INTO `servers` (`servername`, `owner`, `ip`, `port`, `active`, `key`)"
			. "VALUES ('{$db->safe($_POST['name'])}','{$db->safe(Mitrastroi::ToSteamID($player->steamid))}','{$db->safe($_POST['ip'])}','{$db->safe((int)$_POST['port'])}','{$db->safe($active)}','{$db->safe(Mitrastroi::randString(127))}')");
	}
	$alert = ($is) ? '<div class="alert alert-success">Готово ;)</div>' : '<div class="alert alert-danger">Ниа, такого стим айди нет :(</div>';
}

$page_fucking_title = "Добавить сервер";
$menu->set_item_active('server_add');
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");

include Mitrastroi::PathTPL("server_add");

include Mitrastroi::PathTPL("right_side");
include Mitrastroi::PathTPL("footer");