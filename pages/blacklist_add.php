<?php
if (!$tox1n_lenvaya_jopa or !$tox1n_lenvaya_jopa->take_group_info("change_group")) {
	include MITRASTROI_ROOT . "pages/403.php";
	exit();
}
$query = $db->execute("SELECT `txtid`, `name` FROM `groups` WHERE NOT `txtid`='ple' ORDER BY `id`");
$groups = array();
$steamid = (isset($lnk[1]))? $lnk[1]: "";
$groups_options = '';
$alert = '';
while ($group = $db->fetch_array($query)) {
	array_push($groups, $group['txtid']);
	$groups_options .= "\n\t\t\t<option value=\"{$group['txtid']}\">{$group['name']}</option>";
}

if (isset($_POST['reason']) and isset($_POST['steamid'])) {
	if (!strlen($_POST['reason'])) {
		$alert = '<div class="alert alert-danger">Ниа, без причины не получится :(</div>';
	} elseif (strlen($_POST['reason']) > 255) {
		$alert = '<div class="alert alert-danger">Ниа, c такой длинной причиной не получится :(</div>';
	} else {

		$url = "http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=$_STEAMAPI&steamids=" . Mitrastroi::ToCommunityID($_POST['steamid']);
		$json_object = file_get_contents($url);
		if (!$json_object or !strlen($json_object)) die ("werfgjk");
		$json_decoded = json_decode($json_object);
		$is = false;
		foreach ($json_decoded->response->players as $player) {
			$is = true;
			$db->execute("INSERT INTO `user_info_cache` (`steamid`, `steam_url`, `avatar_url`, `nickname`) VALUES ('" . $db->safe(Mitrastroi::ToSteamID($player->steamid)) . "', '" . $db->safe($player->profileurl) . "', '" . $db->safe($player->avatarfull) . "', '" . $db->safe($player->personaname) . "')"
				. "ON DUPLICATE KEY UPDATE `steam_url`='" . $db->safe($player->profileurl) . "', `avatar_url`='" . $db->safe($player->avatarfull) . "', `nickname`='" . $db->safe($player->personaname) . "'") or die($db->error());
			$db->execute("INSERT INTO `blacklist` (`steam_id`, `reason`, `admin`)"
				. "VALUES ('{$db->safe(Mitrastroi::ToSteamID($player->steamid))}','{$db->safe($_POST['reason'])}','{$tox1n_lenvaya_jopa->steamid()}')");
		}
		$alert = ($is)? '<div class="alert alert-success">Готово ;)</div>': '<div class="alert alert-danger">Ниа, такого стим айди нет :(</div>';
	}
}

$page_fucking_title = "Кинуть в ЧС";
$menu->set_item_active('black_add');
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");

include Mitrastroi::PathTPL("blacklist_add");

include Mitrastroi::PathTPL("right_side");
include Mitrastroi::PathTPL("footer");