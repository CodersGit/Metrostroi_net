<?php
if(!isset($lnk[1])) {
	include MITRASTROI_ROOT . "pages/404.php";
	exit();
}

$query = $db->execute("SELECT * FROM `servers` WHERE `id`='{$db->safe($lnk[1])}'");

if (!$db->num_rows($query)) {
	include MITRASTROI_ROOT . "pages/404.php";
	exit();
}

$query = $db->fetch_array($query);

if (!$tox1n_lenvaya_jopa or $tox1n_lenvaya_jopa->icon_id() < 6 or ($tox1n_lenvaya_jopa->steamid() != $query['owner'] and !$tox1n_lenvaya_jopa->take_group_info("admin_panel"))) {
	include MITRASTROI_ROOT . "pages/403.php";
	exit();
}
$alert = '';
$owner = (isset($_POST['owner']) and $tox1n_lenvaya_jopa->take_group_info("admin_panel")) ? $_POST['owner'] : $query['owner'];
$name = $query['servername'];
$ip = $query['ip'];
$port = $query['port'];
$active = $query['active'];
$key = $query['key'];

if (isset($_POST['name']) and isset($_POST['ip']) and isset($_POST['port'])) {
	$active = (isset($_POST['active']))? 1:0;
	$name = $_POST['name'];
	$ip = $_POST['ip'];
	$port = $_POST['port'];

	$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$_STEAMAPI&steamids=" . Mitrastroi::ToCommunityID($query['owner']);
	$json_object = file_get_contents($url);
	if (!$json_object or !strlen($json_object)) die ("Error");
	$json_decoded = json_decode($json_object);
	$is = false;
	$key = (!isset($_POST['key']) or !strlen($_POST['key']))? Mitrastroi::randString(127):$key;
	foreach ($json_decoded->response->players as $player) {
		$is = true;
		$db->execute("INSERT INTO `user_info_cache` (`steamid`, `steam_url`, `avatar_url`, `nickname`) VALUES ('" . $db->safe(Mitrastroi::ToSteamID($player->steamid)) . "', '" . $db->safe($player->profileurl) . "', '" . $db->safe($player->avatarfull) . "', '" . $db->safe($player->personaname) . "')"
			. "ON DUPLICATE KEY UPDATE `steam_url`='" . $db->safe($player->profileurl) . "', `avatar_url`='" . $db->safe($player->avatarfull) . "', `nickname`='" . $db->safe($player->personaname) . "'") or die($db->error());
		$db->execute("UPDATE `servers` SET `servername`='{$db->safe($name)}', `ip`='{$db->safe($ip)}', `port`='{$db->safe($port)}', `key`='{$db->safe($key)}',"
			. " `active`='{$db->safe($active)}', `owner`='{$db->safe($owner)}' WHERE `id`='{$db->safe($lnk[1])}'");
	}
	$alert = ($is) ? '<div class="alert alert-success">Готово ;)</div>' : '<div class="alert alert-danger">Ниа, такого стим айди нет :(</div>';
}

$page_fucking_title = "Редактировать сервер";
$menu->set_item_active('servers');
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");

include Mitrastroi::PathTPL("server_edit");

include Mitrastroi::PathTPL("right_side");
include Mitrastroi::PathTPL("footer");