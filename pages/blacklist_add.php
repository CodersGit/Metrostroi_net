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
		$db->execute("INSERT INTO `blacklist` (`steam_id`, `reason`, `admin`)"
			. "VALUES ('{$db->safe($_POST['steamid'])}','{$db->safe($_POST['reason'])}','{$tox1n_lenvaya_jopa->steamid()}')");
		$alert = '<div class="alert alert-success">Готово ;)</div>';
	}
}

$page_fucking_title = "Кинуть в ЧС";
$menu->set_item_active('black_add');
include Mitrastroi::PathTPL("header");
include Mitrastroi::PathTPL("left_side");

include Mitrastroi::PathTPL("blacklist_add");

include Mitrastroi::PathTPL("right_side");
include Mitrastroi::PathTPL("footer");